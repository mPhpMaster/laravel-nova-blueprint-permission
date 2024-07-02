<?php

namespace Database\Seeders;

use App\Models\Model;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 *
 */
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * @param $directories
     *
     * @return \Illuminate\Support\Collection
     * @throws \ReflectionException
     */
    public static function modelsIn($directories): \Illuminate\Support\Collection
    {
        $namespace = app()->getNamespace();

        $models = [];

        foreach(
            (new Finder())
                ->depth(0)
                ->files()
                ->in($directories)
                ->files() as $model
        ) {
            $model = $namespace . str_replace(
                    [ '/', '.php' ],
                    [ '\\', '' ],
                    Str::after($model->getPathname(), app_path() . DIRECTORY_SEPARATOR)
                );

            if(
                (
                    is_subclass_of($model, Model::class) ||
                    is_subclass_of($model, \Illuminate\Foundation\Auth\User::class)
                ) &&
                !(new ReflectionClass($model))->isAbstract()
            ) {
                $models[] = $model;
            }
        }

        return collect($models)->sort();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \ReflectionException
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        beginForgetCachedPermissions();

        $this->registerPermissions();

        $this->registerRoles();

//        $this->attachPermissionsToAdminRoles();
//        $this->attachPermissionsToSupervisorRole();
//        $this->attachPermissionsToForemanRole();
//        $this->attachPermissionsToEmployeeRole();

        $this->assignAdminsRoles();

        finishForgetCachedPermissions();
    }

    /**
     * dynamic permission creation
     *
     * @throws \ReflectionException
     */
    private function registerPermissions(): void
    {
        static::modelsIn(app_path('Models'))
              ->add(Role::class)
              ->add(Permission::class)
              ->each(function($modelFQN, $key) {
                  if( !class_exists($modelFQN) ) {
                      return;
                  }

                  Permission::insertOrIgnore(\InConfigParser::permissionsOf($modelFQN, $modelFQN));
              });

    }

    /**
     * dynamic roles creations
     */
    private function registerRoles(): void
    {
        Role::insertOrIgnore(\InConfigParser::roles());
    }

    /**
     * Give Users Super-Admin Role
     */
    private function assignAdminsRoles()
    {
        User::byEmail(config('auth.super_admins'))
            ->get()
            ->each(fn(User $user) => $user->assignRole(Role::forSuperAdmin()));
    }

    /**
     * Assign admin Permissions
     */
    private function attachPermissionsToAdminRoles(): void
    {
        // region: assign admin Permissions
        $permissions = Permission::all();
        $roles = collect([
                             'superAdminRole' => Role::forSuperAdmin(),
                             'adminRole' => Role::forAdmin(),
                         ])
            ->unique->id
            ->each->givePermissionTo($permissions);
        // endregion: assign admin Permissions
    }

    /**
     * Assign Supervisor Permissions
     */
    private function attachPermissionsToSupervisorRole()
    {
        /** @var \Illuminate\Support\Collection $permissions */
        $permissions = Permission::forPermission("view", "viewAny")
                                 ->withoutGroup('User', 'Role', 'Permission')
                                 ->get();

        if( $permissions->count() ) {
            Role::forSupervisor()->givePermissionTo($permissions);
        }
    }

    /**
     * Assign Foreman Permissions
     */
    private function attachPermissionsToForemanRole()
    {
        /** @var \Illuminate\Support\Collection $permissions */
        $permissions = Permission::forPermission("viewAny")
                                 ->withoutGroup('User', 'Role', 'Permission')
                                 ->get();

        if( $permissions->count() ) {
            Role::forForeman()->givePermissionTo($permissions);
        }
    }

    /**
     * Assign Employee Permissions
     */
    private function attachPermissionsToEmployeeRole()
    {
        /** @var \Illuminate\Support\Collection $permissions */
        $permissions = Permission::forPermission("view", "viewAny", "create")
                                 ->withoutGroup('User')
                                 ->get();

        if( $permissions->count() ) {
            Role::forEmployee()->givePermissionTo($permissions);
        }
    }
}
