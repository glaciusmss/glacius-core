<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/25/2019
 * Time: 10:01 AM.
 */

namespace App\Services;


use App\Address;
use App\Contracts\Processor;
use App\Enums\AddressType;
use App\Enums\EventType;
use App\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class BaseProcessor extends BaseMarketplace implements Processor
{
    protected $event;

    public function process($event)
    {
        $this->event = $event;

        if (!$type = $this->getEventType()) {
            return;
        }

        /** @var Collection $rawData */
        $rawData = $this->event->rawData;

        $this->getMarketplace()->rawWebhooks()->create([
            'raw_data' => $rawData->toArray()
        ]);

        if ($type->is(EventType::Created())) {
            $this->processWhenCreated($rawData);
        }
    }

    protected function transformAddressAttr($address, $keysToTransform)
    {
        $temp = [];
        foreach ($address as $key => $value) {
            $transformed = false;

            foreach ($keysToTransform as $oriKey => $expectedKey) {
                if ($oriKey === $key) {
                    $temp[$expectedKey] = $value;
                    $transformed = true;
                    break;
                }
            }

            if (!$transformed) {
                $temp[$key] = $value;
            }
        }

        return $temp;
    }

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

        /** @var Address $createdShippingAddress */
        $createdShippingAddress = $record->addAddress(
            Arr::only($address, ['type', 'address1', 'address2', 'city', 'state', 'zip', 'country'])
        );

        $createdShippingAddress->addContact(
            Arr::only($address, ['first_name', 'last_name', 'phone', 'email'])
        );
    }

    abstract protected function getEventType();

    abstract protected function processWhenCreated(Collection $rawData);

    abstract protected function processWhenUpdated(Collection $rawData);
}
