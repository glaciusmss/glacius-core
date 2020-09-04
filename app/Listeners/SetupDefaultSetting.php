<?php

namespace App\Listeners;

use App\Events\OAuthConnected;

class SetupDefaultSetting
{
    public function handle(OAuthConnected $oAuthConnected)
    {
        $oAuthConnected->shop->saveMultipleSettings([
            [
                'label' => 'Product Sync',
                'setting_key' => 'is_product_sync_activated',
                'setting_value' => true,
                'type' => 'boolean',
            ],
            [
                'label' => 'Order Sync',
                'setting_key' => 'is_order_sync_activated',
                'setting_value' => true,
                'type' => 'boolean'
            ],
            [
                'label' => 'Customer Sync',
                'setting_key' => 'is_customer_sync_activated',
                'setting_value' => true,
                'type' => 'boolean'
            ]
        ], $oAuthConnected->marketplace->value);
    }
}
