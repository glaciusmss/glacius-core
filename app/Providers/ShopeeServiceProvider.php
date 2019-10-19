<?php

namespace App\Providers;

use App\Contracts\OAuth as OAuthContract;
use App\Contracts\Webhook as WebhookContract;
use App\Http\Controllers\ShopeeController;
use App\Http\Middleware\Webhook\Shopee as ValidateWebhookMiddleware;
use App\Services\Shopee\Factory;
use App\Services\Shopee\OAuth;
use App\Services\Shopee\Webhook;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\ServiceProvider;

class ShopeeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function (Container $app) {
            $shopeeFactory = new Factory();
            $shopeeFactory->setupSdk(
                $app->make('config')->get('marketplace.shopee')
            );

            return $shopeeFactory;
        });

        $this->app->singleton(Webhook::class, function (Container $app) {
            return new Webhook(
                $app->make(Factory::class)
            );
        });

        $this->app->singleton(OAuth::class, function (Container $app) {
            return new OAuth(
                $app->make(CacheContract::class),
                $app->make(Factory::class)
            );
        });

        $this->app->when(ShopeeController::class)
            ->needs(OAuthContract::class)
            ->give(OAuth::class);

        $this->app->when(ValidateWebhookMiddleware::class)
            ->needs(WebhookContract::class)
            ->give(Webhook::class);
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
