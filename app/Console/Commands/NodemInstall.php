<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class NodemInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nodem:install
                            {--U|username= : The username that will be linked to a new generated invitation code }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare the application to be up and running quickly';

    protected string $logo = <<<'TAG'
        #     #
        ##    #  ####  #####  ###### #    #
        # #   # #    # #    # #      ##  ##
        #  #  # #    # #    # #####  # ## #
        #   # # #    # #    # #      #    #
        #    ## #    # #    # #      #    #
        #     #  ####  #####  ###### #    #
        TAG;

    public function handle(): int
    {
        $this->line(<<<TAG
                Welcome to

                {$this->logo}
                TAG);

        if ($this->tableExists('users') || $this->tableExists('servers')) {
            $this->newLine();
            $this->alert('It seems that you want to re-install the application. This command can be run once and only on an empty database.');
            $this->line('You can reset your database by running `$ php artisan migrate:fresh --force` before this command but be careful, you will lose all your data.');
            $this->newLine();

            return 1;
        }

        $this->newLine();
        $this->line(<<<'TAG'
            I will prepare your application to be up and running quickly by:

            - preparing database tables
            - seeding access control data
            - generating an invitation code to create the owner account
            TAG);

        if (! $this->confirm('Are you ready to start?', true)) {
            $this->line('Ok, see you next time!');

            return 2;
        }

        $this->call('migrate:fresh');

        $this->call('db:seed', ['--class' => 'AccessControlSeeder']);

        $this->newLine();
        $this->warn('Generating invitation code');

        $this->call('user:invite', ['--username' => $this->option('username')]);

        return 0;
    }

    private function tableExists(string $table): bool
    {
        $driver   = (string) config('database.default');
        $database = (string) config(sprintf('database.connections.%s.database', $driver));
        $schema   = $driver === 'pgsql' ? 'public' : $database;

        return collect(DB::select(
            'SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ?',
            [$schema, $table]
        ))->isNotEmpty() && DB::table($table)->count() > 0;
    }
}
