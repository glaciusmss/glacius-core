<?php

namespace App\Providers;

use App\Http\Controllers\FacebookController;
use App\Services\NotificationChannels\Facebook\BotAuth;
use App\Contracts\BotAuth as BotAuthContract;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
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

        $this->app->when(FacebookController::class)
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
