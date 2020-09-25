<?php

namespace App\Utils;

use App\Address;
use App\Enums\AddressType;
use Illuminate\Support\Arr;

trait UpdateAddresses
{
    protected function createBillingAddress($record, $billingAddress)
    {
        $this->createAddress($record, $billingAddress, AddressType::Billing());
    }

    protected function createShippingAddress($record, $shippingAddress)
    {
        $this->createAddress($record, $shippingAddress, AddressType::Shipping());
    }

    protected function createAddress($record, $address, $type = null)
    {
        $address = Arr::add($address, 'type', $type ?? AddressType::Default());

        /** @var Address $createdAddress */
        $createdAddress = $record->addAddress(
            Arr::only($address, ['type', 'address1', 'address2', 'city', 'state', 'zip', 'country'])
        );

        $createdAddress->addContact(
            Arr::only($address, ['first_name', 'last_name', 'phone', 'email'])
        );
    }
}
