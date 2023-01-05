<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 *
 */
class AdminGroupRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[ \Spatie\Permission\PermissionRegistrar::class ]->forgetCachedPermissions();
        $permissions = Permission::all();

        $roles = [];
        foreach( array_wrap(config('permission.super_admin_roles')) as $role ) {
            $role[ 'guard_name' ] ??= getDefaultGuardName('web');
            $roles[] = $current_role = Role::firstOrCreateOrRestore($role);
            $current_role->givePermissionTo($permissions);
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
