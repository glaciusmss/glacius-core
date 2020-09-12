<?php

namespace App\Providers;

use App\Contracts\BotAuth as BotAuthContract;
use App\Http\Controllers\TelegramController;
use App\Services\NotificationChannels\Telegram\BotAuth;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BotAuth::class, function (Container $app) {
            return new BotAuth();
        });

        $this->app->when(TelegramController::class)
            ->needs(BotAuthContract::class)
            ->give(BotAuth::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
