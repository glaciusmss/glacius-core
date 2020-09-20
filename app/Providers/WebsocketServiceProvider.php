<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class WebsocketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('viewWebSocketsDashboard', function ($user = null) {
            return in_array($user->email, [
                'lkloon123@hotmail.com'
            ]);
        });
    }
}
