<?php

namespace App\Providers;

use App\Contracts\OAuth as OAuthContract;
use App\Contracts\ResolvesConnector;
use App\Contracts\Webhook as WebhookContract;
use App\Http\Controllers\EasystoreController;
use App\Http\Middleware\Webhook\Easystore as ValidateWebhookMiddleware;
use App\Services\Easystore\EasystoreConnector;
use App\Services\Easystore\Factory;
use App\Services\Easystore\OAuth;
use App\Services\Easystore\Syncs\SyncProduct;
use App\Services\Easystore\Webhook;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as CacheContract;
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

//        $this->app->singleton(Factory::class, function (Container $app) {
//            $easystoreShop = $app->make('request')->get(
//                'easystore_shop',
//                $app->make('request')->get('shop')
//            );
//
//            $easystoreFactory = new Factory();
//            $easystoreFactory->setupSdk(
//                $app->make('config')->get('marketplace.easystore'),
//                compact('easystoreShop')
//            );
//
//            return $easystoreFactory;
//        });
//
//        $this->app->singleton(Webhook::class, function (Container $app) {
//            return new Webhook(
//                $app->make(Factory::class)
//            );
//        });
//
//        $this->app->singleton(OAuth::class, function (Container $app) {
//            return new OAuth(
//                $app->make(CacheContract::class),
//                $app->make(Factory::class),
//                $app->make(Webhook::class)
//            );
//        });
//
//        $this->registerSync();
//
//        $this->app->when(EasystoreController::class)
//            ->needs(OAuthContract::class)
//            ->give(OAuth::class);
//
//        $this->app->when([EasystoreController::class, ValidateWebhookMiddleware::class])
//            ->needs(WebhookContract::class)
//            ->give(Webhook::class);
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

//    protected function registerSync()
//    {
//        $this->app->singleton(SyncProduct::class, function (Container $app) {
//            return new SyncProduct(
//                $app->make(Factory::class)
//            );
//        });
//    }
}
