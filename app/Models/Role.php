<?php

namespace App\Models;

use App\Interfaces\IRole;
use App\Models\Scopes\Searchable;
use App\Traits\THasScopeName;
use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\RefreshesPermissionCache;

/**
 * @mixin IdeHelperRole
 */
class Role extends \Spatie\Permission\Models\Role implements IRole
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use TModelTranslation;
    use THasScopeName;
    use RefreshesPermissionCache;

    protected $guard_name = 'web';
    protected $fillable = [
        'name',
        'guard_name',
        'group',
    ];
    // protected $attributes = [
    //     'guard_name' => 'web',
    // ];

    protected $searchableFields = [ '*' ];

    protected static function booting()
    {
        static::deleting(function(Role $model) {
            throw_if(!($user = currentUser()) || $user->hasRole($model->name), new \Illuminate\Validation\UnauthorizedException("Unauthorized.", 403));
        });
    }

    public function __construct(array $attributes = [])
    {
        $attributes[ 'guard_name' ] = $attributes[ 'guard_name' ] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
    }

    public function getTable()
    {
        return config('permission.table_names.roles', parent::getTable());
    }

    public static function create(array $attributes = [])
    {
        $attributes[ 'guard_name' ] = $attributes[ 'guard_name' ] ?? 'web';
        $params = [ 'name' => $attributes[ 'name' ], 'guard_name' => $attributes[ 'guard_name' ] ];
        if( PermissionRegistrar::$teams ) {
            if( array_key_exists(PermissionRegistrar::$teamsKey, $attributes) ) {
                $params[ PermissionRegistrar::$teamsKey ] = $attributes[ PermissionRegistrar::$teamsKey ];
            } else {
                $attributes[ PermissionRegistrar::$teamsKey ] = getPermissionsTeamId();
            }
        }
        if( static::findByParam($params) ) {
            throw RoleAlreadyExists::create($attributes[ 'name' ], $attributes[ 'guard_name' ]);
        }

        return static::query()->create($attributes);
    }

    /**
     * @param \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {

    }

    /**
     * @return \Spatie\Permission\Contracts\Role|\App\Interfaces\IRole
     */
    public static function forSuperAdmin(): \Spatie\Permission\Contracts\Role|IRole
    {
        return static::findByName(IRole::SuperAdminRole);
    }
}
