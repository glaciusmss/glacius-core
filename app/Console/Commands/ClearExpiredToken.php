<?php

namespace App\Console\Commands;

use App\Token;
use Illuminate\Console\Command;

class ClearExpiredToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $deletedCount = Token::where('expired_at', '<', now())->delete();
        $this->info("Deleted {$deletedCount} expired tokens");
    }
}
