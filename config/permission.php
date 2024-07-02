<?php

use App\Interfaces\IRole;
use App\Interfaces\IUserType;

return [

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        'permission' => \App\Models\Permission::class,
//         'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        'role' => \App\Models\Role::class,
//         'role' => Spatie\Permission\Models\Role::class,
//         'role' => Sereny\NovaPermissions\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
         */
        'role_pivot_key' => null, //default 'role_id',
        'permission_pivot_key' => null, //default 'permission_id',

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to use the teams feature and your related model's
         * foreign key is other than `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true, the method for checking permissions will be registered on the gate.
     * Set this to false, if you want to implement custom logic for checking permissions.
     */

    'register_permission_check_method' => true,

    /*
     * When set to true the package implements teams using the 'team_foreign_key'. If you want
     * the migrations to register the 'team_foreign_key', you must set this to true
     * before doing the migration. If you already did the migration then you must make a new
     * migration to also add 'team_foreign_key' to 'roles', 'model_has_roles', and
     * 'model_has_permissions'(view the latest version of package's migration file)
     */

    'teams' => false,

    /*
     * When set to true, the required permission names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     */

    'enable_wildcard_permission' => false,

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],

    "super_admin_user" => [
        'name' => 'Admin',
        'password' => 'Admin@123',
        'email' => 'admin@app.com',
        'role'           => IRole::SuperAdminRole,
        'remember_token' => 'sfdsggbesr',
        'email_verified_at' => now(),
    ],

    'super_admin_roles' => [
        $superAdminRole = InConfigParser::roleOf(IRole::SuperAdminRole),

        // $superAdminRole = [ 'name' => IRole::SuperAdminRole, 'group' => 'system', 'guard_name' => 'web' ],
    ],

    'roles' => [
        $superAdminRole,
        InConfigParser::roleOf(IRole::UserRole),
        // InConfigParser::roleOf(IRole::AdminRole, "user"),
        // InConfigParser::roleOf(IRole::ProjectManagerRole, "user"),
        // InConfigParser::roleOf(IRole::SettlementAndReconciliationRole, "user"),
        // InConfigParser::roleOf(IRole::MerchantRole, "user"),

        // [ 'name' => IRole::AdminRole, 'group' => 'user', 'guard_name' => 'web' ],
        // [ 'name' => IRole::ProjectManagerRole, 'group' => 'user', 'guard_name' => 'web' ],
        // [ 'name' => IRole::SettlementAndReconciliationRole, 'group' => 'user', 'guard_name' => 'web' ],
        // [ 'name' => IRole::MerchantRole, 'group' => 'user', 'guard_name' => 'web' ],
    ],

    'permissions' => [
//        ...InConfigParser::permissionsOf("role"),
//        ...InConfigParser::permissionsOf("permission"),
//        ...InConfigParser::permissionsOf("user"),

        // [ 'name' => 'role.index', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.view_any', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.view', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.create', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.edit', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.delete', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.restore', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'role.force_delete', 'group' => 'system', 'guard_name' => 'web' ],

        // [ 'name' => 'permission.index', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.view_any', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.view', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.create', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.edit', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.delete', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.restore', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'permission.force_delete', 'group' => 'system', 'guard_name' => 'web' ],

        // [ 'name' => 'user.index', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.view_any', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.view', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.create', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.edit', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.delete', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.restore', 'group' => 'system', 'guard_name' => 'web' ],
        // [ 'name' => 'user.force_delete', 'group' => 'system', 'guard_name' => 'web' ],
    ],

    /**
     * permissions name map used in controller
     */
    'resource_ability_map'            => [
	    'viewAny' => 'viewAny',
	    'index'   => 'viewAny',

	    'view' => 'view',
	    'show' => 'view',

	    'create' => 'create',
	    'store'  => 'create',

	    'update' => 'update',
	    'edit'   => 'update',

	    'delete'  => 'delete',
	    'destroy' => 'delete',

	    'forceDelete'  => 'forceDelete',
	    'forceDestroy' => 'forceDelete',

	    'restore' => 'restore',
    ],

    /**
     * controller methods that doesn't require model used in controller
     */
    'resource_methods_without_models' => [
	    'index',
	    'create',
	    'store',
    ],
];
