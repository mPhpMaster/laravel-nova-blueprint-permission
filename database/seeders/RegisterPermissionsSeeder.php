<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Register All Permissions
 */
class RegisterPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[ PermissionRegistrar::class ]->forgetCachedPermissions();

        toCollect(config('permission.permissions', []))
            ->map(fn($p) => Permission::firstOrCreate($p));

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
