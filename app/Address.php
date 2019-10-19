<?php

namespace App;

use App\Enums\AddressType;
use App\Utils\HasContact;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Address
 *
 * @property int $id
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property int $type
 * @property string|null $addressable_type
 * @property int|null $addressable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $addressable
 * @property-read \App\Contact $contact
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereAddressableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereAddressableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Address whereZip($value)
 * @mixin \Eloquent
 */
class Address extends Model
{
    use HasContact;

    protected $fillable = [
        'address1', 'address2', 'city', 'state', 'zip', 'country', 'type'
    ];

    protected $enumCasts = [
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
