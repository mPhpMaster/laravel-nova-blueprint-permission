<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

/**
 *
 */
class RegisterAdminRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:register:admin:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register unregistered [ADMIN Roles] and assign all Permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAliases([
                              'register:admin:roles',
                              'rar',
                          ]);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('permission:cache-reset');

        $results = [];
        $count = [ 'exist' => 0, 'created' => 0, ];

        $permissions = Permission::all();
        $super_admin_roles = array_wrap(config('permission.super_admin_roles'));
        $roles = [];
        foreach( $super_admin_roles as $r ) {
            if( $role = Role::where(array_only($r, 'name'))->first() ) {
                $roles[] = $role;
                $role->givePermissionTo($permissions);

                $results[] = [ 'Role' => "<info>{$role->name}</info>", 'Status' => '<error>EXIST!</error>' ];
                $count[ 'exist' ]++;
                continue;
            }

            $roles[] = $role = Role::firstOrCreateOrRestore($r);//->refresh();
            $role->givePermissionTo($permissions);

            $results[] = [ 'Role' => "<info>{$role->name}</info>", 'Status' => '<question>CREATED!</question>' ];
            $count[ 'created' ]++;
        }

        $this->table([ 'Role', 'Status' ], $results);

        $this->info("<question>" . count($super_admin_roles) . "</question> Total Roles");
        $this->info("<question>{$count[ 'created' ]}</question> created.");
        $this->info("<question>{$count[ 'exist' ]}</question> exists.");

        $this->call('permission:cache-reset');

        return 0;
    }
}
