<?php

namespace App\Listeners;

use App\Events\OAuthConnected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetupDefaultSetting
{
    public function handle(OAuthConnected $oAuthConnected)
    {
        $oAuthConnected->shop->saveMultipleSettings([
            'is_product_sync_activated' => true,
        ], $oAuthConnected->marketplace->value);
    }
}
