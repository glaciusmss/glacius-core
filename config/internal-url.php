<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Internal Private URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by internal communication
    |
    */

    'app_url' => env('INTERNAL_APP_URL', env('APP_URL', 'http://localhost')),
    'db_url' => env('INTERNAL_DB_URL', env('DB_HOST', '127.0.0.1')),
    'redis_url' => env('INTERNAL_REDIS_URL', env('REDIS_HOST', '127.0.0.1')),
    'websocket_url' => env('INTERNAL_WEBSOCKET_URL', '127.0.0.1'),
];
