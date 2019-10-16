<?php

namespace App;

use App\Events\Order\OrderCreated;
use App\Events\Order\OrderUpdated;
use App\Utils\HasAddresses;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @property int $id
 * @property float $total_price
 * @property float $subtotal_price
 * @property array|null $meta
 * @property int $shop_id
 * @property int $marketplace_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Marketplace $marketplace
 * @property-read \App\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereSubtotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Address[] $addresses
 * @property-read int|null $addresses_count
 */
class Order extends Model
{
    use HasAddresses;

    protected $fillable = [
        'total_price', 'subtotal_price', 'type', 'meta', 'marketplace_id',
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
        'updated' => OrderUpdated::class,
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_details')
            ->withTimestamps()
            ->withPivot(['quantity'])
            ->using(OrderDetails::class);
    }
}
