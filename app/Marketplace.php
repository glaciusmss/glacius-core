<?php

namespace App;

use App\Scopes\CustomerScope;
use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperMarketplace
 */
class Marketplace extends Model
{
    use OrderScope, CustomerScope;

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'marketplace_integrations')
            ->withTimestamps()
            ->withPivot(['token', 'refreshToken', 'meta'])
            ->using(MarketplaceIntegration::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function rawWebhooks()
    {
        return $this->hasMany(RawWebhook::class);
    }

    public function syncTransactions()
    {
        return $this->hasMany(SyncTransaction::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
