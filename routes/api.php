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

Route::prefix('/shopify')->group(function () {
    Route::get('/oauth', 'ShopifyController@store');
    Route::post('/webhooks', 'ShopifyController@webhooks');
});

Route::prefix('/shopee')->group(function () {
    Route::get('/oauth', 'ShopeeController@store');
    Route::get('/oauth/delete', 'ShopeeController@delete');
    Route::post('/webhooks', 'ShopeeController@webhooks');
});

Route::prefix('/woocommerce')->group(function () {
    Route::post('/callback', 'WoocommerceController@store');
    Route::get('/redirect', 'WoocommerceController@redirect');
    Route::post('/webhooks', 'WoocommerceController@webhooks');
});

Route::prefix('/easystore')->group(function () {
    Route::get('/oauth', 'EasystoreController@store');
    Route::post('/webhooks', 'EasystoreController@webhooks');
});

//require auth
Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::apiResources([
        'shop' => 'ShopController',
        'product' => 'ProductController',
    ]);

    Route::apiResource('order', 'OrderController')->only(['index', 'show']);
    Route::apiResource('customer', 'CustomerController')->only(['index', 'show']);

    Route::prefix('/image')->group(function () {
        Route::get('/{image}', 'MediaController@getImage');
        Route::post('/', 'MediaController@storeImage');
    });

    Route::get('/marketplace-integration', 'MarketplaceIntegrationController@index');

    Route::prefix('/shopify')->group(function () {
        Route::post('/oauth', 'ShopifyController@create');
        Route::delete('/oauth', 'ShopifyController@destroy');
    });

    Route::prefix('/shopee')->group(function () {
        Route::post('/oauth', 'ShopeeController@create');
        Route::delete('/oauth', 'ShopeeController@destroy');
    });

    Route::prefix('/woocommerce')->group(function () {
        Route::post('/oauth', 'WoocommerceController@create');
        Route::delete('/oauth', 'WoocommerceController@destroy');
    });

    Route::prefix('/easystore')->group(function () {
        Route::post('/oauth', 'EasystoreController@create');
        Route::delete('/oauth', 'EasystoreController@destroy');
    });

    Route::prefix('/notification')->group(function () {
        Route::get('/', 'ConnectedNotificationChannelController@index');

        Route::prefix('/telegram')->group(function () {
            Route::post('/', 'TelegramController@connect');
            Route::delete('/', 'TelegramController@disconnect');
        });
    });

    Route::prefix('/setting')->group(function () {
        Route::get('/{collection}', 'SettingController@show');
        Route::patch('/{collection}', 'SettingController@update');
    });
});

Route::fallback(function () {
    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('page not found');
});
