<?php

namespace App;

use App\Enums\AddressType;
use App\Utils\HasContact;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAddress
 */
class Address extends Model
{
    use HasContact;

    protected $fillable = [
        'address1', 'address2', 'city', 'state', 'zip', 'country', 'type'
    ];

    protected $casts = [
        'type' => AddressType::class,
    ];

    protected $touches = [
        'addressable'
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
