<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;


use App\Address;

trait HasAddresses
{
    /**
     * @return Address
     */
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function addAddress(array $attributes)
    {
        return $this->addresses()->create($attributes);
    }

    public function updateAddress(Address $address, array $attributes)
    {
        return $address->fill($attributes)->save();
    }

    public function deleteAddress(Address $address)
    {
        if ($this !== $address->addressable()->first()) {
            return false;
        }

        return $address->delete();
    }
}
