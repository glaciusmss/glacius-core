<?php

namespace App\Console\Commands;

use App\Models\Token;
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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $deletedCount = Token::expired()->delete();
        $this->info("Deleted {$deletedCount} expired tokens");
    }
}
