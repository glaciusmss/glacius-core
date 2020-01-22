<?php

namespace App;

use App\Scopes\PaginationScope;
use App\Scopes\PeriodScope;
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
 * @property int|null $customer_id
 * @property-read \App\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPeriodLastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPeriodLastQuarter()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPeriodLastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPeriodLastYear()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPeriodToday()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPeriodYesterday()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPreviousPeriodLastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPreviousPeriodLastQuarter()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPreviousPeriodLastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPreviousPeriodLastYear()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPreviousPeriodToday()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order createdByPreviousPeriodYesterday()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order withPagination(\App\DTO\Pagination $pagination)
 */
class Order extends Model
{
    use HasAddresses, PeriodScope, PaginationScope;

    protected $perPage = 10;

    protected $fillable = [
        'total_price', 'subtotal_price', 'type', 'meta', 'marketplace_id',
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_details')
            ->withTimestamps()
            ->withPivot(['quantity'])
            ->using(OrderDetails::class);
    }
}
