<?php

namespace Database\Seeders\Stories;

use App\Interfaces\IRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 *
 */
class Story1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory([
                                  'role' => IRole::UserRole,
                              ])
                    ->create();

    }
}
