<?php

namespace Database\Seeders;

use App\Console\Commands\CreateAdminCommand;
use Illuminate\Database\Seeder;

/**
 *
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(RegisterRolesSeeder::class);
        // $this->call(RegisterPermissionsSeeder::class);
        // $this->call(AdminGroupRoleSeeder::class);
        // \Artisan::call(CreateAdminCommand::class);

        $this->call(Story1Seeder::class);
        // $this->call(Story1Seeder::class);
        // $this->call(UserSeeder::class);
    }
}
