<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Marketplace
 *
 * @property int $id
 * @property string $name
 * @property string $website
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RawWebhook[] $rawWebhooks
 * @property-read int|null $raw_webhooks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Shop[] $shops
 * @property-read int|null $shops_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Marketplace whereWebsite($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SyncTransaction[] $syncTransactions
 * @property-read int|null $sync_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Customer[] $customers
 * @property-read int|null $customers_count
 */
class Marketplace extends Model
{
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
