<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/20/2019
 * Time: 10:09 AM.
 */

namespace App\Services\Woocommerce\Processors;


use App\Customer;
use App\Enums\EventType;
use App\Enums\MarketplaceEnum;
use App\Enums\Woocommerce\WebhookTopic;
use App\Services\BaseProcessor;
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
            ->wherePivot('meta->woocommerce_store_url', $this->event->rawData->get('woocommerce_store_url'))
            ->first();
    }

    protected function mapWebhookTopicToEventType()
    {
        if (WebhookTopic::CustomerCreate()->is($this->event->topic)) {
            return EventType::Created();
        }

        if (WebhookTopic::CustomerUpdate()->is($this->event->topic)) {
            return EventType::Updated();
        }

        if (WebhookTopic::CustomerDelete()->is($this->event->topic)) {
            return EventType::Deleted();
        }

        return null;
    }

    protected function processFor()
    {
        return Customer::class;
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

        $this->log('created customer record', $customerRecord->toArray());

        return $customerRecord;
    }

    protected function processWhenUpdated(Collection $rawData)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop()->customers()
            ->where('meta->marketplace_customer_id', $rawData->get('id'))
            ->first();

        if (!$customerRecord) {
            return null;
        }

        $this->log('previous customer record', $customerRecord->toArray());

        $customerRecord->updateContact([
            'first_name' => $rawData->get('first_name'),
            'last_name' => $rawData->get('last_name'),
            'email' => $rawData->get('email'),
            'phone' => $rawData->get('phone'),
        ]);

        $this->log('updated customer record', $customerRecord->toArray());

        return $customerRecord;
    }

    protected function processWhenDeleted(Collection $rawData)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop()->customers()
            ->where('meta->marketplace_customer_id', $rawData->get('id'))
            ->first();

        if ($customerRecord) {
            $customerRecord->delete();
        }

        $this->log('deleted customer record', $customerRecord->toArray());

        return $customerRecord;
    }
}
