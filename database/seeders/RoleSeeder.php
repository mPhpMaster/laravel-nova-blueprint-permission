<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 *
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return Role::factory()
            ->count(5)
            ->create()
            ->each(function(Role $role) {
                $role->givePermissionTo(Permission::inRandomOrder()->take(random_int(1, 10))->get());
            });
    }
}
