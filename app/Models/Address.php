<?php

namespace App\Models;

use App\Enums\AddressType;
use App\Models\MorphHelper\HasContact;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Address
 *
 * @property int $id
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property AddressType $type
 * @property string|null $addressable_type
 * @property int|null $addressable_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read Model|\Eloquent $addressable
 * @property-read \App\Models\Contact|null $contact
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddressableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereAddressableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereZip($value)
 * @mixin \Eloquent
 */
class Address extends Model
{
    use HasContact, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => AddressType::class,
    ];

    protected $touches = [
        'addressable',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
