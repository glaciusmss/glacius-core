<?php

namespace App\Providers;

use App\Contracts\OAuth as OAuthContract;
use App\Contracts\Processor as ProcessorContract;
use App\Contracts\Webhook as WebhookContract;
use App\Events\Webhook\OrderCreateReceivedFromMarketplace;
use App\Http\Controllers\ShopifyController;
use App\Http\Middleware\Webhook\Shopify as ValidateWebhookMiddleware;
use App\Services\Shopify\Factory;
use App\Services\Shopify\OAuth;
use App\Services\Shopify\Processors\CustomerProcessor;
use App\Services\Shopify\Processors\OrderProcessor;
use App\Services\Shopify\Syncs\SyncProduct;
use App\Services\Shopify\Webhook;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ShopifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Factory::class, function (Container $app) {
            $shopifyShop = $app->make('request')->input(
                'shopify_shop',
                $app->make('request')->input('shop')
            );

            $shopifyFactory = new Factory();
            $shopifyFactory->setupSdk(
                $app->make('config')->get('marketplace.shopify'),
                compact('shopifyShop')
            );

            return $shopifyFactory;
        });

        $this->app->singleton(Webhook::class, function (Container $app) {
            return new Webhook(
                $app->make('config')->get('marketplace.shopify'),
                $app->make(CacheContract::class),
                $app->make(Factory::class)
            );
        });

        $this->app->singleton(OAuth::class, function (Container $app) {
            return new OAuth(
                $app->make('config')->get('marketplace.shopify'),
                $app->make(CacheContract::class),
                $app->make(Factory::class),
                $app->make(Webhook::class)
            );
        });

        $this->registerProcessors();

        $this->registerSync();

        $this->app->when(ShopifyController::class)
            ->needs(OAuthContract::class)
            ->give(OAuth::class);

        $this->app->when([ShopifyController::class, ValidateWebhookMiddleware::class])
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
                $app->make('config')->get('marketplace.shopify'),
                $app->make(CacheContract::class),
                $app->make(Factory::class)
            );
        });
    }

    protected function registerProcessors()
    {
        $this->app->singleton(OrderProcessor::class, function (Container $app) {
            return new OrderProcessor(
                $app->make('config')->get('marketplace.shopify'),
                $app->make(CacheContract::class)
            );
        });

        $this->app->singleton(CustomerProcessor::class, function (Container $app) {
            return new CustomerProcessor(
                $app->make('config')->get('marketplace.shopify'),
                $app->make(CacheContract::class)
            );
        });
    }
}
