<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 *
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = call_user_func($this->resolve(RoleSeeder::class));

        User::factory()
            ->count(5)
            ->create()
            ->each(function(User $user) use ($roles) {
                $user->assignRole(
                    $roles[ random_int(0, count($roles) - 1) ]
                );
            });
    }
}
