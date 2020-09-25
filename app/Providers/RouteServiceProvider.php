<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Route::bind('shop', function ($value) {
            return throw_unless(
                \Auth::user()->shops()->find($value),
                new NotFoundHttpException('shop not found')
            );
        });

        Route::bind('product', function ($value) {
            $shop = $this->app->make(Controller::class)->getShop();

            return throw_unless(
                $shop->products()->find($value),
                new NotFoundHttpException('product not found')
            );
        });

        Route::bind('order', function ($value) {
            $shop = $this->app->make(Controller::class)->getShop();

            return throw_unless(
                $shop->orders()->find($value),
                new NotFoundHttpException('order not found')
            );
        });

        Route::bind('image', function ($value) {
            return throw_unless(
                Media::whereFileName($value)->first(),
                new NotFoundHttpException('image not found')
            );
        });

        Route::bind('userProfile', function ($value) {
            return throw_unless(
                \Auth::user()->userProfile()->find($value),
                new NotFoundHttpException('user not found')
            );
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

//        $this->mapWebRoutes();

        $this->mapBotManCommands();
    }

    /**
     * Defines the BotMan "hears" commands.
     *
     * @return void
     */
    protected function mapBotManCommands()
    {
        require base_path('routes/botman.php');
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
