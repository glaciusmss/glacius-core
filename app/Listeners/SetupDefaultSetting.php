<?php

namespace App\Listeners;

use App\Events\OAuthConnected;

class SetupDefaultSetting
{
    public function handle(OAuthConnected $oAuthConnected)
    {
        $oAuthConnected->shop->saveMultipleSettings([
            'is_product_sync_activated' => true,
        ], $oAuthConnected->marketplace->value);
    }
}
