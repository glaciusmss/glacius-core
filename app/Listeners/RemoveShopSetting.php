<?php

namespace App\Listeners;

use App\Events\OAuthDisconnected;

class RemoveShopSetting
{
    public function handle(OAuthDisconnected $oAuthDisconnected)
    {
        $oAuthDisconnected->shop->deleteAllSettingsFromCollection($oAuthDisconnected->marketplace->value);
    }
}
