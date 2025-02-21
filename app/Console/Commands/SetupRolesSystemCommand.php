<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;

/**
 *
 */
class SetupRolesSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Roles System.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAliases([
                              'setup:roles',
                              'sr',
                          ]);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		// forMe: register permissions by models names
	    $this->call('db:seed', [ '--class' => RolesAndPermissionsSeeder::class ]);
        $this->call('permission:cache-reset');

        $permissions = toCollect(config('permission.permissions', []))
            ->map(fn($p) => Permission::firstOrCreateOrRestore($p))
            ->count();

        $roles = toCollect(config('permission.roles', []))
            ->map(fn($p) => \App\Models\Role::firstOrCreateOrRestore($p))
            ->count();

        $this->call('permission:cache-reset');

        $rows = compact('permissions', 'roles');
        $this->table(array_keys($rows), [ $rows ]);

        return Command::SUCCESS;
    }
}
