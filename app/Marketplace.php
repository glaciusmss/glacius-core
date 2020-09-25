<?php

namespace App;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Scopes\CustomerScope;
use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Marketplace.
 *
 * @property int $id
 * @property string $name
 * @property string $website
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Customer[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\RawWebhook[] $rawWebhooks
 * @property-read int|null $raw_webhooks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Shop[] $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SyncTransaction[] $syncTransactions
 * @property-read int|null $sync_transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace query()
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Marketplace whereWebsite($value)
 * @mixin \Eloquent
 */
class Marketplace extends Model
{
    use OrderScope, CustomerScope, EagerLoadPivotTrait;

    protected $guarded = [];

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
