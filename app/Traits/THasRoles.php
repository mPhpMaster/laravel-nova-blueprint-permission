<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $role
 * @mixin \App\Models\User
 */
trait THasRoles
{
    use HasRoles;

    /**
     * @var array
     */
    protected array $queuedRoles = [];

    /**
     * @var
     */
    private $roleClass;

    public static function bootTHasRoles()
    {
        static::created(function($model) {
            $model->assignQueuedRoles();
        });

        static::deleting(function($model) {
            if( method_exists($model, 'isForceDeleting') && !$model->isForceDeleting() ) {
                return;
            }

            $model->roles()->detach();
        });
    }

    /**
     * A model may have multiple roles.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        );
    }

    /**
     * @param array $columns
     *
     * @return static|null
     */
    public function role($columns = [ '*' ]): ?\App\Models\Role
    {
        return $this->roles()->first($columns);
    }

    /**
     * Scope the model query to certain roles only.
     *
     * @param \Illuminate\Database\Eloquent\Builder                                         $query
     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRole(Builder $query, string|null ...$role): Builder
    {
        $roles = empty($roles = array_filter($role)) ? ['supervisor'] : $roles;

        $roles = array_map(function($role) {
            if( $role instanceof Role ) {
                return $role;
            }

            $method = is_numeric($role) ? 'findById' : 'findByName';

            return $this->getRoleClass()->{$method}($role, $this->getDefaultGuardName());
        }, array_wrap($roles));

        return $query->whereHas('roles', function($query) use ($roles) {
            $query->where(function($query) use ($roles) {
                foreach( $roles as $role ) {
                    $query->orWhere(config('permission.table_names.roles') . '.id', $role->id);
                }
            });
        });
    }

    /**
     * Assign the given role to the model.
     *
     * @param array|string|\Spatie\Permission\Contracts\Role ...$roles
     *
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function($role) {
                if( empty($role) ) {
                    return false;
                }

                return $this->getStoredRole($role);
            })
            ->filter(function($role) {
                return $role instanceof Role;
            })
            ->each(function($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->map->id
            ->all();

        $model = $this->getModel();

        if( $model->exists ) {
            $this->roles()->sync($roles, false);
            $model->load('roles');
        } else {
            /** @var \Illuminate\Database\Eloquent\Model $class */
            $class = \get_class($model);

            $class::saved(
                function($object) use ($roles, $model) {
                    static $modelLastFiredOn;
                    if( $modelLastFiredOn !== null && $modelLastFiredOn === $model ) {
                        return;
                    }
                    $object->roles()->sync($roles, false);
                    $object->load('roles');
                    $modelLastFiredOn = $object;
                }
            );
        }

        $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * Revoke the given role from the model.
     *
     * @param string|\Spatie\Permission\Contracts\Role $role
     */
    public function removeRole($role)
    {
        $this->roles()->detach($this->getStoredRole($role));

        $this->load('roles');

        if( is_a($this, get_class($this->getPermissionClass())) ) {
            $this->forgetCachedPermissions();
        }

        return $this;
    }

    /**
     * Remove all current roles and set the given ones.
     *
     * @param array|\Spatie\Permission\Contracts\Role|string ...$roles
     *
     * @return $this
     */
    public function syncRoles(...$roles)
    {
        $this->roles()->detach();

        return $this->assignRole($roles);
    }

    /**
     * Determine if the model has (one of) the given role(n role(s).
     *
     * @param string|int|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if( is_string($roles) && false !== strpos($roles, '|') ) {
            $roles = $this->convertPipeToArray($roles);
        }

        if( is_string($roles) ) {
            return $this->roles->contains('name', $roles);
        }

        if( is_int($roles) ) {
            return $this->roles->contains('id', $roles);
        }

        if( $roles instanceof Role ) {
            return $this->roles->contains('id', $roles->id);
        }

        if( is_array($roles) ) {
            foreach( $roles as $role ) {
                if( $this->hasRole($role) ) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    /**
     * Determine if the model has any of the given role(s).
     *
     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAnyRole($roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Determine if the model has all of the given role(s).
     *
     * @param string|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAllRoles($roles): bool
    {
        if( is_string($roles) && false !== strpos($roles, '|') ) {
            $roles = $this->convertPipeToArray($roles);
        }

        if( is_string($roles) ) {
            return $this->roles->contains('name', $roles);
        }

        if( $roles instanceof Role ) {
            return $this->roles->contains('id', $roles->id);
        }

        $roles = collect()->make($roles)->map(function($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $roles->intersect($this->roles->pluck('name')) == $roles;
    }

    /**
     * Return all permissions directly coupled to the model.
     */
    public function getDirectPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getRoleNames(): Collection
    {
        return $this->roles()->pluck('name');
    }

    /**
     * @param $role
     *
     * @return \Spatie\Permission\Contracts\Role
     */
    protected function getStoredRole($role): Role
    {
        $roleClass = $this->getRoleClass();

        if( is_numeric($role) ) {
            return $roleClass->findById($role, $this->getDefaultGuardName());
        }

        if( is_string($role) ) {
            return $roleClass->findByName($role, $this->getDefaultGuardName());
        }

        return $role;
    }

    /**
     * @param string $pipeString
     *
     * @return string|string[]
     */
    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if( strlen($pipeString) <= 2 ) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if( $quoteCharacter !== $endCharacter ) {
            return explode('|', $pipeString);
        }

        if( !in_array($quoteCharacter, [ "'", '"' ]) ) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }

    public function assignQueuedRoles(): static
    {
        $this->assignRole($this->queuedRoles);
        $this->queuedRoles = [];

        return $this;
    }

    public function getRoleName(): ?string
    {
        return $this->role([ 'name' ])?->name;
    }

    // region: role attribute

    /**
     * @alias $role
     * @return string|null
     */
    public function getRoleAttribute(): ?string
    {
        return $this->getRoleName();
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setRoleAttribute($value)
    {
        $value = array_flatten(array_wrap(value($value)));
        if( empty($value) ) {
            $this->roles()->detach();

            return;
        }

        if( !$this->exists ) {
            $this->queuedRoles[] = $value;

            return;
        }
        $this->assignRole($value);
    }
    // endregion: role attribute


    /**
     * Determine if the model may perform the given permission.
     *
     * @param  string|int|\Spatie\Permission\Contracts\Permission  $permission
     * @param  string|null  $guardName
     * @return bool
     *
     * @throws PermissionDoesNotExist
     */
    public function hasPermissionTo($permission, $guardName = 'web'): bool
    {
        if ($this->getWildcardClass()) {
            return $this->hasWildcardPermission($permission, $guardName);
        }

        $permission = $this->filterPermission($permission, $guardName);

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }
}
