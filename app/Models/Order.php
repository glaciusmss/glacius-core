<?php

namespace App\Models;

use App\Scopes\PaginationScope;
use App\Scopes\PeriodScope;
use App\SearchEngine\IndexConfigurators\OrderIndexConfigurator;
use App\Models\MorphHelper\HasAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $total_price
 * @property string $subtotal_price
 * @property array $meta
 * @property int $shop_id
 * @property int $marketplace_id
 * @property int|null $customer_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Models\Customer|null $customer
 * @property \ScoutElastic\Highlight|null $highlight
 * @property-read \App\Models\Marketplace $marketplace
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\Models\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPeriodLastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPeriodLastQuarter()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPeriodLastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPeriodLastYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPeriodToday()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPeriodYesterday()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPreviousPeriodLastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPreviousPeriodLastQuarter()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPreviousPeriodLastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPreviousPeriodLastYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPreviousPeriodToday()
 * @method static \Illuminate\Database\Eloquent\Builder|Order createdByPreviousPeriodYesterday()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSubtotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order withPagination(\App\DTO\Pagination $pagination)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasAddresses, PeriodScope, PaginationScope, Searchable, HasFactory;

    protected $indexConfigurator = OrderIndexConfigurator::class;

    protected $mapping = [
        'properties' => [
            'id' => ['type' => 'keyword'],
            'total_price' => ['type' => 'keyword'],
            'subtotal_price' => ['type' => 'keyword'],
            'marketplace_name' => ['type' => 'text'],
            'shop_id' => ['type' => 'keyword'],
            'created_at' => ['type' => 'keyword'],
        ],
    ];

    protected $perPage = 10;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'subtotal_price' => $this->subtotal_price,
            'marketplace_name' => $this->marketplace->name,
            'shop_id' => $this->shop_id,
            'created_at' => $this->created_at,
        ];
    }

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
            ->using(OrderDetail::class);
    }
}
