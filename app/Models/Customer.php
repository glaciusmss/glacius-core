<?php

namespace App\Models;

use App\Models\MorphHelper\HasContact;
use App\Scopes\OrderScope;
use App\Scopes\PaginationScope;
use App\Scopes\PeriodScope;
use App\SearchEngine\IndexConfigurators\CustomerIndexConfigurator;
use App\Models\MorphHelper\HasAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property array $meta
 * @property int $shop_id
 * @property int $marketplace_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Models\Contact|null $contact
 * @property \ScoutElastic\Highlight|null $highlight
 * @property-read \App\Models\Marketplace $marketplace
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPeriodLastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPeriodLastQuarter()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPeriodLastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPeriodLastYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPeriodToday()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPeriodYesterday()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPreviousPeriodLastMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPreviousPeriodLastQuarter()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPreviousPeriodLastWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPreviousPeriodLastYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPreviousPeriodToday()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer createdByPreviousPeriodYesterday()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withPagination(\App\DTO\Pagination $pagination)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasAddresses, HasContact, OrderScope, PeriodScope, PaginationScope, Searchable, HasFactory;

    protected $indexConfigurator = CustomerIndexConfigurator::class;

    protected $mapping = [
        'properties' => [
            'id' => ['type' => 'keyword'],
            'email' => ['type' => 'keyword'],
            'first_name' => ['type' => 'keyword'],
            'last_name' => ['type' => 'keyword'],
            'phone' => ['type' => 'keyword'],
            'marketplace_name' => ['type' => 'text'],
            'shop_id' => ['type' => 'keyword'],
            'created_at' => ['type' => 'keyword'],
        ],
    ];

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'email' => $this->contact->email,
            'first_name' => $this->contact->first_name,
            'last_name' => $this->contact->last_name,
            'phone' => $this->contact->phone,
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
