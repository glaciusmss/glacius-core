<?php

namespace App\Services\Shopify;

use App\Contracts\ResolvesConnector;
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
        $this->app->resolving(ResolvesConnector::class, function (ResolvesConnector $connectorResolver) {
            $connectorResolver->addConnector(ShopifyConnector::class);

            return $connectorResolver;
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
