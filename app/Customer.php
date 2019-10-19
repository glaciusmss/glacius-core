<?php

namespace App;

use App\Utils\HasAddresses;
use App\Utils\HasContact;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Customer
 *
 * @property int $id
 * @property array|null $meta
 * @property int $shop_id
 * @property int $marketplace_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Contact $contact
 * @property-read \App\Marketplace $marketplace
 * @property-read \App\Shop $shop
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 */
class Customer extends Model
{
    use HasAddresses, HasContact;

    protected $fillable = [
        'meta', 'marketplace_id'
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
