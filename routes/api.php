<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::match(['get', 'post'], '/botman', function () {
    $botman = app('botman');
    $botman->middleware->received(new App\Botman\Middleware\AuthenticateBotUser());
    $botman->listen();
});

Route::prefix('/user')->group(function () {
    Route::prefix('/login')->group(function () {
        Route::post('/', 'UserController@login');
        Route::post('/{socialProvider}/callback', 'UserController@socialLoginCallback');
        Route::post('/{socialProvider}', 'UserController@socialLogin');
    });
    Route::post('/register', 'UserController@register');
    Route::patch('/password', 'UserController@password');
    Route::post('/logout', 'UserController@logout');
    Route::get('/me', 'UserController@me');

    Route::prefix('/email')->group(function () {
        Route::post('/resend', 'UserController@resendEmailVerification')->name('verification.resend');
        Route::get('/verify/{id}/{hash}', 'UserController@verifyEmailVerification')->name('verification.verify');
        Route::get('/is-verified', function () {
            return response()->json();
        })->middleware('verified');
    });
});

Route::prefix('/{identifier}')->middleware('supported.marketplace')->group(function () {
    Route::post('/callback', 'ConnectorController@woocommerceCallback');
    Route::get('/oauth', 'ConnectorController@oAuthCallback');
    Route::get('/redirect', 'ConnectorController@woocommerceRedirect');
    Route::post('/webhooks', 'ConnectorController@webhooks')->middleware('validate.webhook');
});

//require auth
Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::apiResources([
        'shop' => 'ShopController',
        'product' => 'ProductController',
    ]);

    Route::apiResource('order', 'OrderController')->only(['index', 'show']);
    Route::apiResource('customer', 'CustomerController')->only(['index', 'show']);
    Route::apiResource('user_profile', 'UserProfileController')->only(['index', 'update']);

    Route::prefix('/image')->group(function () {
        Route::get('/{image}', 'MediaController@getImage');
        Route::post('/', 'MediaController@storeImage');
    });

    Route::get('/marketplace-integration', 'MarketplaceIntegrationController@index');

    Route::prefix('/statistic')->group(function () {
        Route::get('/marketplace-sales', 'StatisticController@marketplaceSales');
        Route::get('/new-customer', 'StatisticController@newCustomer');
    });

    Route::prefix('/{identifier}')->middleware('supported.marketplace')->group(function () {
        Route::post('/oauth', 'ConnectorController@create');
        Route::delete('/oauth', 'ConnectorController@destroy');
    });

    Route::prefix('/notification')->group(function () {
        Route::get('/', 'ConnectedNotificationChannelController@index');

        Route::prefix('/telegram')->group(function () {
            Route::post('/', 'TelegramController@connect');
            Route::delete('/', 'TelegramController@disconnect');
        });

        Route::prefix('/facebook')->group(function () {
            Route::post('/', 'FacebookController@connect');
            Route::delete('/', 'FacebookController@disconnect');
        });
    });

    Route::prefix('/setting')->group(function () {
        Route::get('/', 'SettingController@index');
        Route::get('/{identifier}', 'SettingController@show');
        Route::patch('/{identifier}', 'SettingController@update');
    });
});

Route::fallback(function () {
    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('page not found');
});
