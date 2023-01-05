<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

/**
 *
 */
class RegisterPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:register:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register unregistered permissions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAliases([
                              'register:permissions',
                              'rp',
                          ]);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $results = [];
        $count = [ 'exist' => 0, 'created' => 0, ];
        $permissions = toCollect(config('permission.permissions', []))
            ->map(function($p) use (&$results, &$count) {
                if( $permission = Permission::where($p)->first() ) {
                    $results[] = [ 'Permission' => "<info>{$permission->name}</info>", 'Status' => '<error>EXIST!</error>' ];
                    $count[ 'exist' ]++;

                    return null;
                }

                $status = "CREATED";
                $permission = Permission::firstOrCreate($p)->refresh();
                if( $permission->trashed() ) {
                    $permission->restore();
                    $status = "RESTORED";
                }
                $results[] = [ 'Permission' => "<info>{$permission->name}</info>", 'Status' => "<question>{$status}!</question>" ];
                $count[ 'created' ]++;

                return $permission;
            });

        $this->table([ 'Permission', 'Status' ], $results);

        $this->info("<question>" . $permissions->count() . "</question> Total Permissions");
        $this->info("<question>{$count[ 'created' ]}</question> created.");
        $this->info("<question>{$count[ 'exist' ]}</question> exists.");

        return 0;
    }
}
