<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use App\Traits\THasScopeName;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Guard;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\RefreshesPermissionCache;

/**
 * @mixin IdeHelperPermission
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use THasScopeName;
    use RefreshesPermissionCache;

    protected $fillable = [
        'name',
        'guard_name',
        'group',
    ];
    // protected $attributes = [
    //     'guard_name' => 'web',
    // ];

    protected $searchableFields = [ '*' ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('permission.table_names.permissions', parent::getTable());
    }

    /**
     * Save a new model and return the instance.
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model|$this
     * @static
     * @noinspection PhpHierarchyChecksInspection
     */
    public static function create(array $attributes = [])
    {
        $attributes[ 'guard_name' ] = $attributes[ 'guard_name' ] ?? 'web';

        $permission = static::getPermission([ 'name' => $attributes[ 'name' ], 'guard_name' => $attributes[ 'guard_name' ] ]);

        if( $permission ) {
            throw PermissionAlreadyExists::create($attributes[ 'name' ], $attributes[ 'guard_name' ]);
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
     * Get the current cached permissions.
     *
     * @param array $params
     * @param bool  $onlyOne
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected static function getPermissions(array $params = [], bool $onlyOne = false): Collection
    {
        return app(PermissionRegistrar::class)
            ->setPermissionClass(static::class)
            ->getPermissions($params, $onlyOne);
    }

    /**
     * Get the current cached first permission.
     *
     * @param array $params
     *
     * @return \Spatie\Permission\Contracts\Permission
     */
    protected static function getPermission(array $params = []): ?PermissionContract
    {
        return static::getPermissions($params, true)->first();
    }
}
