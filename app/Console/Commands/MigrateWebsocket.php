<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateWebsocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Websocket migration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('migrate', [
            '--database' => config('database.websocket_connection'),
            '--path' => config('database.websocket_migration_path'),
        ]);

        return 0;
    }
}
