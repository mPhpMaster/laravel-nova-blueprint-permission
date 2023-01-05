<?php

namespace Database\Seeders;

use App\Interfaces\IRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use InConfigParser;

/**
 *
 */
class Story1Seeder extends Seeder
{
    public static function map(): array
    {
        return [
            null => [
                // IRole::AdminRole => [
                //     'users' => [
                //         [ "admin-roles", "admin", 'Admin@123', 'admin@admin.com' ],
                //     ],
                //     'permissions' => [
                //         "role",
                //     ],
                // ],
                'roles-view' => [
                    'users' => [
                        [ "roles-view", 'Admin@123', 'role@view.com' ],
                    ],
                    'permissions' => [
                        "role.index",
                        "role.view",
                        "role.view_any",
                    ],
                ],
                'roles-edit' => [
                    'users' => [
                        [ "roles-edit", 'Admin@123', 'role@edit.com' ],
                    ],
                    'permissions' => [
                        "role.edit",
                        "role.create",
                    ],
                ],
                'roles-delete' => [
                    'users' => [
                        [ "roles-delete", 'Admin@123', 'role@delete.com' ],
                    ],
                    'permissions' => [
                        "role.delete",
                        "role.restore",
                        "role.force_delete",
                    ],
                ],
            ],

        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RegisterPermissionsSeeder::class);

        foreach( static::map() as $group => $roles ) {
            foreach( $roles as $role => $data ) {
                /** @var Role $role */
                $role = Role::firstOrCreateOrRestore(...array_only_except(InConfigParser::roleOf($role, "story"), [ 'name', 'guard_name' ]));
                dump("Role: {$role->id}. {$role->name} generated!");
                $group && $group->assignRole($role);

                [ 'users' => $users, 'permissions' => $permissions ] = $data;
                $permissions = toCollect($permissions)->flatMap(fn($name) => InConfigParser::permissionsOf($name, "story"))
                                                      ->map(fn($permission) => Permission::firstOrCreateOrRestore(...array_only_except($permission, [ 'name', 'guard_name' ])))
                                                      ->all();

                $role->givePermissionTo($permissions);

                foreach( $users as $user ) {
                    /** @var User $user */
                    $user = User::firstOrCreateOrRestore(InConfigParser::userOf(...array_wrap($user)));
                    dump("User: {$user->id}. {$user->email} generated!");

                    $user->assignRole($role);
                }
            }
        }
    }
}
