<?php

namespace App\Providers;

use App\Contracts\OAuth as OAuthContract;
use App\Contracts\Webhook as WebhookContract;
use App\Http\Controllers\WoocommerceController;
use App\Http\Middleware\Webhook\Woocommerce as ValidateWebhookMiddleware;
use App\Services\Woocommerce\Factory;
use App\Services\Woocommerce\OAuth;
use App\Services\Woocommerce\Syncs\SyncProduct;
use App\Services\Woocommerce\Webhook;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class WoocommerceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function (Container $app) {
            $woocommerceStoreUrl = $app->make('request')->input('woocommerce_store_url');

            if (!Str::endsWith($woocommerceStoreUrl, '/')) {
                $woocommerceStoreUrl .= '/';
            }

            $woocommerceFactory = new Factory();
            $woocommerceFactory->setupSdk(
                $app->make('config')->get('marketplace.woocommerce'),
                ['url' => $woocommerceStoreUrl]
            );

            return $woocommerceFactory;
        });

        $this->app->singleton(Webhook::class, function (Container $app) {
            return new Webhook(
                $app->make(Factory::class)
            );
        });

        $this->app->singleton(OAuth::class, function (Container $app) {
            return new OAuth(
                $app->make(CacheContract::class),
                $app->make(Factory::class),
                $app->make(Webhook::class)
            );
        });

        $this->registerSync();

        $this->app->when(WoocommerceController::class)
            ->needs(OAuthContract::class)
            ->give(OAuth::class);

        $this->app->when([WoocommerceController::class, ValidateWebhookMiddleware::class])
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

    protected function registerSync()
    {
        $this->app->singleton(SyncProduct::class, function (Container $app) {
            return new SyncProduct(
                $app->make(Factory::class)
            );
        });
    }
}
