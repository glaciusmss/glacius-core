<?php

namespace App;

use App\Scopes\OrderScope;
use App\Scopes\PaginationScope;
use App\Scopes\PeriodScope;
use App\Utils\HasAddresses;
use App\Utils\HasContact;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCustomer
 */
class Customer extends Model
{
    use HasAddresses, HasContact, OrderScope, PeriodScope, PaginationScope;

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
