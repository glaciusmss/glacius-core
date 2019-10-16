<?php

namespace App\Listeners;

use App\Events\OAuthDisconnected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveShopSetting
{
    public function handle(OAuthDisconnected $oAuthDisconnected)
    {
        $oAuthDisconnected->shop->deleteAllSettingsFromCollection($oAuthDisconnected->marketplace->value);
    }
}
