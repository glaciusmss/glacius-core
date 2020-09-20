<?php

namespace App\Services\Easystore;

use App\Contracts\ResolvesConnector;
use Illuminate\Support\ServiceProvider;

class EasystoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving(ResolvesConnector::class, function (ResolvesConnector $connectorResolver) {
            $connectorResolver->addConnector(EasystoreConnector::class);

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
