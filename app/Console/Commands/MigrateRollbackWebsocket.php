<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateRollbackWebsocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollback:websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Websocket migration rollback';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('migrate:rollback', [
            '--database' => config('database.websocket_connection'),
            '--path' => config('database.websocket_migration_path'),
        ]);

        return 0;
    }
}
