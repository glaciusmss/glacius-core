<?php

namespace App\Providers;

use App\Contracts\ResolvesConnector;
use App\Services\Connectors\ConnectorManager;
use App\Services\Connectors\ConnectorResolver;
use Illuminate\Support\ServiceProvider;

class ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ConnectorResolver::class, static function () {
            return new ConnectorResolver();
        });

        $this->app->bind(ResolvesConnector::class, ConnectorResolver::class);

        $this->app->singleton(ConnectorManager::class, static function () {
            return new ConnectorManager();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
