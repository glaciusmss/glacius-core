<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 2/19/2020
 * Time: 3:08 PM.
 */

namespace App\Providers;


use App\Botman\Controllers\Commands\DisconnectCommand;
use App\Botman\Controllers\Commands\StartCommand;
use App\Contracts\BotAuth as BotAuthContract;
use App\Enums\BotPlatform;
use App\Services\NotificationChannels\Facebook\BotAuth as FacebookBotAuth;
use App\Services\NotificationChannels\Telegram\BotAuth as TelegramBotAuth;
use Carbon\Laravel\ServiceProvider;

class BotmanServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when([StartCommand::class, DisconnectCommand::class])
            ->needs(BotAuthContract::class)
            ->give(function () {
                $platform = strtolower(\BotMan::getDriver()->getName());

                if (BotPlatform::Telegram()->is($platform)) {
                    return $this->app->make(TelegramBotAuth::class);
                }

                if (BotPlatform::Facebook()->is($platform)) {
                    return $this->app->make(FacebookBotAuth::class);
                }
            });
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
