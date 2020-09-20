<?php

namespace App\Services\Woocommerce;

use App\Contracts\ResolvesConnector;
use Illuminate\Support\ServiceProvider;

class WoocommerceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving(ResolvesConnector::class, function (ResolvesConnector $connectorResolver) {
            $connectorResolver->addConnector(WoocommerceConnector::class);

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
