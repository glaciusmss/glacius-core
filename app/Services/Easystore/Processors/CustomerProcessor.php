<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/20/2019
 * Time: 10:09 AM.
 */

namespace App\Services\Easystore\Processors;


use App\Customer;
use App\Enums\Easystore\WebhookTopic;
use App\Enums\EventType;
use App\Enums\MarketplaceEnum;
use App\Services\BaseProcessor;
use Illuminate\Support\Collection;

class CustomerProcessor extends BaseProcessor
{
    public function name()
    {
        return MarketplaceEnum::EasyStore();
    }

    public function getShop($withRelations = null)
    {
        if ($this->shop) {
            return $this->shop;
        }

        return $this->shop = $this->getMarketplace()
            ->shops()
            ->wherePivot('meta->easystore_shop', $this->event->rawData->get('shop_domain'))
            ->first();
    }

    protected function getEventType()
    {
        if (WebhookTopic::CustomerCreate()->is($this->event->topic)) {
            return EventType::Created();
        }

        return null;
    }

    protected function processWhenCreated(Collection $rawData)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop()->customers()->create([
            'meta' => ['marketplace_customer_id' => $rawData->get('customer_id')],
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
                    $this->transformAddressAttr($address, ['province' => 'state'])
                );
            }
        }

        return $customerRecord;
    }

    protected function processWhenUpdated(Collection $rawData)
    {
        // TODO: Implement processWhenUpdated() method.
    }

    protected function processWhenDeleted(Collection $rawData)
    {
        // TODO: Implement processWhenDeleted() method.
    }
}
