<?php

namespace Database\Seeders;

use App\Console\Commands\CreateAdminCommand;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Seeder;
use App\Interfaces\IRole;
use Database\Seeders\Stories\Story1Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 *
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//		$this->call(RolesAndPermissionsSeeder::class);
//		Artisan::call('app:admin');
	    // $this->call(RegisterRolesSeeder::class);
	    // $this->call(RegisterPermissionsSeeder::class);
	    // $this->call(AdminGroupRoleSeeder::class);
	    // \Artisan::call(CreateAdminCommand::class);

		// $this->call(Story1Seeder::class);
	    // $this->call(Story1Seeder::class);
	    // $this->call(UserSeeder::class);
    }
}
