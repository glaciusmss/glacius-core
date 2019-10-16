<?php

namespace App\Console\Commands;

use App\TempMedia;
use Illuminate\Console\Command;

class ClearExpiredImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired image';

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
        $query = TempMedia::where('created_at', '<', now()->subMinutes(30));
        $tempImages = $query->get();
        $deletedCount = $query->delete();

        foreach ($tempImages as $tempImage) {
            \Storage::delete($tempImage->path);
        }

        $this->info("Deleted {$deletedCount} expired images");
    }
}
