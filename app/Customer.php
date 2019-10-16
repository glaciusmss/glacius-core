<?php

namespace App;

use App\Utils\HasAddresses;
use App\Utils\HasContact;
use Illuminate\Database\Eloquent\Model;

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
}
