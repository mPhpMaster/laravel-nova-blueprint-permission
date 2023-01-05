<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 */
class SetupCommand extends Command
{
    /** @var bool */
    const SHOW_OPTIONS = !true;

    /**
     * @var array[]
     */
    public static array $commands = [
        [ 'app:setup:roles', [] ], // register all permissions and roles
        [ 'app:register:admin:roles', [] ],
        [ 'app:admin', [] ],
    ];
    private array $commandsQueue = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run App Setup.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setAliases([
                              'setup',
                              '+',
                          ]);
        $this->addOption('new', 'N', InputOption::VALUE_NONE, 'Run new setup.');
        $this->addOption('migrate', 'm', InputOption::VALUE_NONE, 'Run migrations.');
        $this->addOption('fresh', 'f', InputOption::VALUE_NONE, 'Run fresh migrations.');
        $this->addOption('seed', 's', InputOption::VALUE_NONE, 'Run seeders.');

        $this->addOption('admin', 'a', InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE, 'Force create admin.');
        $this->addOption('no-verify', 'A', InputOption::VALUE_NONE, "Don't verify admin.");

        $this->addOption('exception', 'E', InputOption::VALUE_NONE, 'Die on exception.');
    }

    /**
     * @return string[]
     */
    public static function adminAttributes(): array
    {
        return config('permission.super_admin_user');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if( $this->getOutput()->getVerbosity() > OutputInterface::VERBOSITY_NORMAL ) {
            dd($this->option());
        }

        if( $this->option('new') ) {
            static::SHOW_OPTIONS && $this->line("--new");

            $this->input->setOption('migrate', true);
            $this->input->setOption('fresh', false);
            $this->input->setOption('seed', false);
            $this->input->setOption('no-verify', false);
        }

        $this->migrateCommand();
        $this->runCommands();
        $this->adminCommand();
        $this->queuedCommand();

        $this->newLine();
        $this->alert("Setup Done!️");

        return Command::SUCCESS;
    }

    public function dieOnException(?\Throwable $exception = null)
    {
        if( $exception ) {
            if( $this->option('exception') ) {
                throw $exception;
            } else {
                $this->error($exception->getMessage());
            }
        }
    }

    /**
     * @return \App\Models\User|\Illuminate\Database\Eloquent\Model
     */
    public function createSuperAdminUser()
    {
        $this->newLine();
        $this->line("Create Super Admin User");
        $adminAttrs = static::adminAttributes();

        static::SHOW_OPTIONS && $this->line("--admin,--no-admin");
        if( $admin = $this->option('admin') ) {
            if( $user = User::where(array_only($adminAttrs, [ 'email' ]))->first() ) {
                $this->newLine();
                $this->question("Force Delete: {$user->name}");
                $user->forceDelete();
            }
            $this->newLine();
            $this->question("Force Create: ");// . implode(" ", array_values(array_only($adminAttrs, [ 'first_name', 'last_name' ]))));
            $admin = User::create($adminAttrs);
            $this->info("[{$admin->name}] {$admin->email}#{$admin->getKey()}");
        } elseif( is_null($admin) ) {
            $this->newLine();
            $this->question("Create If Not Exist: ");// . implode(" ", array_values(array_only($adminAttrs, [ 'first_name', 'last_name' ]))) . "");

            $admin = User::firstOrCreateOrRestore(...array_only_except($adminAttrs, [ 'email' ]));
            $this->info("[{$admin->name}] {$admin->email}#{$admin->getKey()}");
        } else {
            $this->question("Skip Admin Creation...");
        }

        if( $admin ) {
            static::SHOW_OPTIONS && $this->line("--no-verify");
            if( !($noVerify = $this->option('no-verify')) ) {
                $this->newLine();
                $this->question("Verifying Admin Email...");
                if( !$admin->hasVerifiedEmail() ) {
                    $admin->markEmailAsVerified();
                }
            }

            $method = $admin->hasVerifiedEmail() ? "info" : "error";
            $this->$method("[{$admin->name}] {$admin->email}#{$admin->getKey()} Is: " . ($admin->hasVerifiedEmail() ? "" : "NOT ") . "Verified!");
        }

        return $admin;
    }

    public function migrateCommand()
    {
        $cmd = [ "", [] ];
        if( $migrate = $this->option('migrate') ) {
            static::SHOW_OPTIONS && $this->line("--migrate");
            $cmd = [ 'migrate', [] ];
        }
        if( $fresh = $this->option('fresh') ) {
            static::SHOW_OPTIONS && $this->line("--fresh");
            $cmd = [ "migrate:fresh", [] ];
        }
        if( $seed = $this->option('seed') ) {
            static::SHOW_OPTIONS && $this->line("--seed");
            $this->commandsQueue[] = [ "db:seed", [] ];
            // if( $cmd[ 0 ] ) {
            //     $cmd[ 1 ] = [ "--seed" => true ];
            // } else {
            //     $cmd = [ "db:seed", [] ];
            // }
        }

        if( count(array_filter($cmd)) ) {
            $this->newLine();
            $this->question("$ " . head($cmd) . " " . implode(" ", last($cmd)));
            $this->call(head($cmd), array_wrap(last($cmd)));
        } else {
            // $this->alert("No Command");
        }
    }

    public function adminCommand()
    {
        if( $noAdmin = $this->option('no-admin') ) {
            static::SHOW_OPTIONS && $this->line("--no-admin");
            // $this->alert("No Admin");
        } else {
            try {
                $this->createSuperAdminUser();
            } catch(\Exception|\Error $exception) {
                $this->dieOnException($exception);
            }
        }
    }

    private function queuedCommand()
    {
        $this->newLine();
        $this->line("Queued Commands: " . count(array_wrap($this->commandsQueue)));
        foreach( $this->commandsQueue as $cmd ) {
            if( count(array_filter($cmd)) ) {
                $this->newLine();
                $this->question("$ " . head($cmd) . " " . implode(" ", last($cmd)));
                $this->call(head($cmd), array_wrap(last($cmd)));
            }
        }
    }

    public function runCommands()
    {
        $this->newLine();
        $this->line("Commands: " . count(array_wrap(static::$commands)));
        foreach( static::$commands as $cmd ) {
            if( count(array_filter($cmd)) ) {
                $this->newLine();
                $this->question("$ " . head($cmd) . " " . implode(" ", last($cmd)));
                $this->call(head($cmd), array_wrap(last($cmd)));
            }
        }
    }
}
