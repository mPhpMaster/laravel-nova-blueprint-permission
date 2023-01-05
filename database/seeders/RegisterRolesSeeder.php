<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Register All Roles
 */
class RegisterRolesSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[ PermissionRegistrar::class ]->forgetCachedPermissions();

        toCollect(config('permission.roles', []))
            ->map(fn($p) => \App\Models\Role::firstOrCreate($p));

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
