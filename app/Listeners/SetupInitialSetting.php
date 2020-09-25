<?php

namespace App\Listeners;

use App\Events\OAuthConnected;

class SetupInitialSetting
{
    public function handle(OAuthConnected $oAuthConnected)
    {
        $marketplace = $oAuthConnected->shop->marketplaces()
            ->whereName($oAuthConnected->identifier)
            ->firstOrFail();

        $marketplace->pivot->createMultipleSettings([
            [
                'label' => 'Product Sync',
                'setting_key' => 'is_product_sync_activated',
                'setting_value' => true,
                'type' => 'boolean',
            ],
            //            [
            //                'label' => 'Order Sync',
            //                'setting_key' => 'is_order_sync_activated',
            //                'setting_value' => true,
            //                'type' => 'boolean'
            //            ],
            [
                'label' => 'Customer Sync',
                'setting_key' => 'is_customer_sync_activated',
                'setting_value' => true,
                'type' => 'boolean',
            ],
        ], 'sync');
    }
}
