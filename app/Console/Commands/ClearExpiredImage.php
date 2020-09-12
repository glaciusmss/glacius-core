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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $query = TempMedia::expired();
        $tempImages = $query->get();
        $deletedCount = $query->delete();

        foreach ($tempImages as $tempImage) {
            \Storage::delete($tempImage->path);
        }

        $this->info("Deleted {$deletedCount} expired images");
    }
}
