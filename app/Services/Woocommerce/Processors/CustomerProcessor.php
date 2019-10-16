<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/20/2019
 * Time: 10:09 AM.
 */

namespace App\Services\Woocommerce\Processors;


use App\Address;
use App\Customer;
use App\Enums\AddressType;
use App\Enums\EventType;
use App\Enums\MarketplaceEnum;
use App\Events\Webhook\CustomerCreateReceivedFromMarketplace;
use App\Product;
use App\Services\BaseProcessor;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CustomerProcessor extends BaseProcessor
{
    public function name()
    {
        return MarketplaceEnum::WooCommerce();
    }

    public function getShop($withRelations = null)
    {
        if ($this->shop) {
            return $this->shop;
        }

        return $this->shop = $this->getMarketplace()
            ->shops()
            ->wherePivot('meta->woocommerce_store_url', '=', $this->event->rawData->get('woocommerce_store_url'))
            ->first();
    }

    protected function getEventType()
    {
        if ($this->event instanceof CustomerCreateReceivedFromMarketplace) {
            return EventType::Created();
        }

        return null;
    }

    protected function processWhenCreated(Collection $rawData)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop()->customers()->create([
            'meta' => ['marketplace_customer_id' => $rawData->get('id')],
            'marketplace_id' => $this->getMarketplace()->id,
        ]);

        $customerRecord->addContact([
            'first_name' => $rawData->get('first_name'),
            'last_name' => $rawData->get('last_name'),
            'email' => $rawData->get('email'),
            'phone' => $rawData->get('phone'),
        ]);

        if ($addresses = $rawData->get('address')) {
            foreach ($addresses as $address) {
                $this->createAddress(
                    $customerRecord,
                    $this->transformAddressAttr($address, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
                );
            }
        }
    }

    protected function processWhenUpdated(Collection $rawData)
    {
        // TODO: Implement processWhenUpdated() method.
    }
}
