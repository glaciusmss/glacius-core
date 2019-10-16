<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/17/2019
 * Time: 10:33 AM.
 */

return [

    'shopify' => [
        'key' => env('SHOPIFY_API_KEY'),
        'secret' => env('SHOPIFY_API_SECRET'),
        'redirect_url' => env('SHOPIFY_REDIRECT_URL'),
        'webhook_url' => env('SHOPIFY_WEBHOOK_URL'),
    ],

    'shopee' => [
        'id' => env('SHOPEE_PARTNER_ID'),
        'key' => env('SHOPEE_PARTNER_KEY'),
        'redirect_url' => env('SHOPEE_REDIRECT_URL'),
        'webhook_url' => env('SHOPEE_WEBHOOK_URL'),
    ],

    'woocommerce' => [
        'secret' => env('WOOCOMMERCE_SECRET'),
        'timeout' => env('WOOCOMMERCE_TIMEOUT', 60),
        'callback_url' => env('WOOCOMMERCE_CALLBACK_URL'),
        'return_url' => env('WOOCOMMERCE_RETURN_URL'),
        'webhook_url' => env('WOOCOMMERCE_WEBHOOK_URL'),
    ],

    'easystore' => [
        'key' => env('EASYSTORE_CLIENT_ID'),
        'secret' => env('EASYSTORE_CLIENT_SECRET'),
        'redirect_url' => env('EASYSTORE_REDIRECT_URL'),
        'webhook_url' => env('EASYSTORE_WEBHOOK_URL'),
    ],

];
