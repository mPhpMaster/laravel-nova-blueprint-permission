<?php

namespace Database\Seeders;

use App\Interfaces\IRole;
use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Permission;
use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * @deprecated
 */
class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[ PermissionRegistrar::class ]->forgetCachedPermissions();


        // Create default Groups
        Permission::create([ 'name' => 'list groups' ]);
        Permission::create([ 'name' => 'view groups' ]);
        Permission::create([ 'name' => 'create groups' ]);
        Permission::create([ 'name' => 'update groups' ]);
        Permission::create([ 'name' => 'delete groups' ]);

        Permission::create([ 'name' => 'list roles' ]);
        Permission::create([ 'name' => 'view roles' ]);
        Permission::create([ 'name' => 'create roles' ]);
        Permission::create([ 'name' => 'update roles' ]);
        Permission::create([ 'name' => 'delete roles' ]);

        // Create user role and assign existing permissions
        $currentPermissions = Permission::all();
        $userRole = Role::create([ 'name' => 'user' ]);
        $userRole->givePermissionTo($currentPermissions);

        // Create admin exclusive permissions
        // Permission::create(['name' => 'list roles']);
        // Permission::create(['name' => 'view roles']);
        // Permission::create(['name' => 'create roles']);
        // Permission::create(['name' => 'update roles']);
        // Permission::create(['name' => 'delete roles']);

        Permission::create([ 'name' => 'list permissions' ]);
        Permission::create([ 'name' => 'view permissions' ]);
        Permission::create([ 'name' => 'create permissions' ]);
        Permission::create([ 'name' => 'update permissions' ]);
        Permission::create([ 'name' => 'delete permissions' ]);

        Permission::create([ 'name' => 'list users' ]);
        Permission::create([ 'name' => 'view users' ]);
        Permission::create([ 'name' => 'create users' ]);
        Permission::create([ 'name' => 'update users' ]);
        Permission::create([ 'name' => 'delete users' ]);

        // Create admin role and assign all permissions
        $allPermissions = Permission::all();
        $adminRole = Role::create([ 'name' => IRole::SuperAdminRole ]);
        $adminRole->givePermissionTo($allPermissions);

        $admin = \App\Models\User::whereEmail('admin@app.com')->first();

        if( $admin ) {
            $admin->assignRole($adminRole);
        }

        if( $user = \App\Models\User::whereEmail('user@app.com')->first() ) {
            $user->assignRole($userRole);
        }
    }
}
