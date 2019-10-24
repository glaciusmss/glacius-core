<?php

namespace App\Providers;

use App\Utils\CarbonFix;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Date::useClass(CarbonFix::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //dont append data on api resources
        Resource::withoutWrapping();

        //use web guard for telescope and horizon only
        if (request()->is(['horizon*', 'telescope*'])) {
            \Auth::shouldUse('web');
        }
    }
}
