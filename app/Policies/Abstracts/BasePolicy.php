<?php

namespace App\Policies\Abstracts;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy Action:Default Permission:
 *  * viewAny:Allowed
 *  * view:Disallowed
 *  * create:Disallowed
 *  * update:Disallowed
 *  * delete:Disallowed
 *  * forceDelete:Disallowed
 *  * restore:Disallowed
 *  * add{Model}:Allowed
 *  * attach{Model}:Allowed
 *  * detach{Model}:Allowed
 */
class BasePolicy
{
    use HandlesAuthorization;

    public static string $permission_name;
    public static bool $super_admin_only = false;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function viewAny(?Authenticatable $user = null)
    {
        return static::userCan("view_any", $user) || $this->index($user);
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function index(?Authenticatable $user)
    {
        return static::userCan("index", $user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User|null               $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function view(?User $user, Model $model)
    {
        return static::userCan("view", $user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return static::userCan("create", $user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function update(User $user, Model $model)
    {
        return static::userCan("edit", $user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function delete(User $user, Model $model)
    {
        return static::userCan("delete", $user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function restore(User $user, Model $model)
    {
        return static::userCan("restore", $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function forceDelete(User $user, Model $model)
    {
        return isDeveloper('alsafadi') || static::userCan("force_delete", $user);
    }

    /**
     * @return bool
     */
    public static function hasPermissionName(): bool
    {
        return !is_null(static::$permission_name);
    }

    /**
     * @return string
     */
    public static function getPermissionName(): ?string
    {
        return static::$permission_name ?? snake_case(str_singular(str_before_last_count(class_basename(static::class), 'Policy')));
    }

    public static function getFullPermissionName(string $permission): ?string
    {
        $permission_name = static::getPermissionName();

        if( is_null($permission_name) ) {
            return $permission;
        }

        return "{$permission_name}.$permission";
    }

    public static function userCan(string $permission, ?User $user = null): bool
    {
        $user ??= currentUser();
        if( !$user ) {
            return false;
        }

        if( static::$super_admin_only && !$user->isSuperAdmin() ) {
            return false;
        }

        return $user->can(static::getFullPermissionName($permission));
    }

    /**
     * Check if the relation is already attached
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Database\Eloquent\Model        $role
     * @param \Illuminate\Database\Eloquent\Model        $permission
     *
     * @return bool
     */
    public function canAttach(Authenticatable $user, \Illuminate\Database\Eloquent\Model $role, \Illuminate\Database\Eloquent\Model $permission)
    {
        $fromMethod = data_get(last(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)), 'function');
        $relationFromMethod = $fromMethod ? str_after($fromMethod, "attach") : null;
        if( $relationFromMethod ) {
            $relationFromMethodPlural = str_plural(camel_case($relationFromMethod));
            if( $role->$relationFromMethodPlural->contains($permission->getKeyName(), $permission->getKey()) ) {
                return false;
            } else {
                return method_exists($this, "attachAny{$relationFromMethod}") ? $this->{"attachAny{$relationFromMethod}"}($user, $role) : true;
            }
        }

        return true;
    }
}
