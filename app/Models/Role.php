<?php

namespace App\Models;

use App\Helpers\Classes\InConfigParser;
use App\Interfaces\IRole;
use App\Models\Scopes\Searchable;
use App\Traits\THasScopeName;
use App\Traits\TModelTranslation;
use App\Traits\TScopePermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Sereny\NovaPermissions\Traits\SupportsRole;
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
	use TScopePermission;
    use RefreshesPermissionCache;
	use SupportsRole;
	use \App\Traits\THasPermissionGroup;

	/** @type string */
	public const PERMISSION = 'Role';

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

    public function __construct(array $attributes = [])
    {
        $attributes[ 'guard_name' ] = $attributes[ 'guard_name' ] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
    }

    public static function create(array $attributes = [])
    {
        $attributes[ 'guard_name' ] = $attributes[ 'guard_name' ] ?? 'web';
        $params = [ 'name' => $attributes[ 'name' ], 'guard_name' => $attributes[ 'guard_name' ] ];

        if( static::findByParam($params) ) {
            throw RoleAlreadyExists::create($attributes[ 'name' ], $attributes[ 'guard_name' ]);
        }

        return static::query()->create($attributes);
    }

	/**
	 * @return \Spatie\Permission\Contracts\Role|\App\Interfaces\IRole
	 */
	public static function forSuperAdmin(): \Spatie\Permission\Contracts\Role|IRole
	{
		return static::findByName(IRole::SuperAdminRole);
	}

	public static function getAllRolesTranslated(): Collection
	{
		return collect(static::getAllRoles())->mapWithKeys(fn($v, $k) => [ $v => static::trans($v) ]);
	}

	protected static function booting()
	{
		static::deleting(function(Role $model) {
			throw_if(!($user = currentUser()) || $user->hasRole($model->name), new \Illuminate\Validation\UnauthorizedException("Unauthorized.", 403));
		});
	}

	public function getTable()
	{
		return config('permission.table_names.roles', parent::getTable());
	}

	public function scopeGetAllRoles(Builder $builder, bool $only_names = true, ...$except): Collection
	{
		return toCollect(InConfigParser::roles())
			->when($only_names, fn($c) => $c->map(fn($a) => data_get(array_wrap($a), 'name')))
			->reject(fn($a) => $a && in_array($a, array_filter($except)));
	}

    /**
     * @param \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {

    }

	public function getNameAttribute()
	{
		return \App\Models\Role::getAllRolesTranslated()->get($this->attributes['name'] ?? '', $this->attributes['name'] ?? '');
	}

	public static function forAdmin(): \Spatie\Permission\Contracts\Role|IRole|null
	{
		return static::findByName(IRole::AdminRole);
    }
}
