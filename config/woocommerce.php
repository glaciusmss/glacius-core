<?php

return [
    'secret' => env('WOOCOMMERCE_SECRET'),
    'timeout' => env('WOOCOMMERCE_TIMEOUT', 60),
    'callback_url' => env('WOOCOMMERCE_CALLBACK_URL'),
    'return_url' => env('WOOCOMMERCE_RETURN_URL'),
    'webhook_url' => env('WOOCOMMERCE_WEBHOOK_URL'),
];
