<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 */
class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user with roles.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAliases([
                              'admin',
                          ]);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update user if exists.');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');

        $this->alert($this->description);
        $this->info("MODE: " . ($force ? "<error>FORCE</error>" : "<question>NORMAL</question>") . "");
        $this->newLine();

        $admin_data = config('permission.super_admin_user');
        $admin = \App\Models\User::firstOrCreateOrRestore(...array_only_except($admin_data, [ 'email' ]));
        if( $force ) {
            $admin->update($admin_data);
        }

        $roles = toCollect(config('permission.super_admin_roles'))->map->name->toArray();
        if( count($roles) && ($roles = Role::byName($roles)->get())->count() ) {
            $admin->assignRole($roles);
        }

        $_admin = $admin->only([ 'id', ...array_keys($admin_data) ]);
        $this->table(array_keys($_admin), [ $_admin ]);

        $_admin = [];
        $_admin[ 'roles' ] ??= $admin->roles()->get([ 'id', 'name' ])->toArray();
        $_admin[ 'roles' ] = implode(", ", array_map(fn($r) => "{$r[ 'id' ]}. {$r[ 'name' ]}", $_admin[ 'roles' ]));

        $this->table(array_keys($_admin), [ $_admin ]);

        return 0;
    }
}
