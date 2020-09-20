<?php

namespace App\Console;

use App\Console\Commands\ClearExpiredImage;
use App\Console\Commands\ClearExpiredToken;
use App\Console\Commands\MigrateRollbackWebsocket;
use App\Console\Commands\MigrateWebsocket;
use BeyondCode\LaravelWebSockets\Console\CleanStatistics;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Telescope\Console\PruneCommand;
use Yadahan\AuthenticationLog\Console\ClearCommand as ClearAuthenticationLogCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ClearExpiredToken::class,
        ClearExpiredImage::class,
        MigrateWebsocket::class,
        MigrateRollbackWebsocket::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(ClearExpiredToken::class)->daily()->runInBackground();
        $schedule->command(ClearExpiredImage::class)->daily()->runInBackground();
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes()->runInBackground();
        $schedule->command(PruneCommand::class)->daily()->runInBackground();
        $schedule->command(CleanStatistics::class)->daily()->runInBackground();
        $schedule->command(ClearAuthenticationLogCommand::class)->daily()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
