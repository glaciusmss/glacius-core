<?php

namespace App\Providers;

use App\Contracts\ResolvesConnector;
use App\Services\Easystore\EasystoreConnector;
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
