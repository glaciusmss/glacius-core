<?php

namespace App;

use App\Scopes\PaginationScope;
use App\Scopes\PeriodScope;
use App\Utils\HasAddresses;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperOrder
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
