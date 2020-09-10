<?php


namespace App\Services\Woocommerce\Processors;


use App\Contracts\Processor;
use App\Customer;
use App\Events\Webhook\WebhookReceived;

class CustomerProcessor extends BaseProcessor implements Processor
{
    public function onCreate(WebhookReceived $event)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop($event)->customers()->create([
            'meta' => ['marketplace_customer_id' => $event->rawData->get('id')],
            'marketplace_id' => $this->getMarketplace($event->identifier)->id,
        ]);

        $customerRecord->addContact([
            'first_name' => $event->rawData->get('first_name'),
            'last_name' => $event->rawData->get('last_name'),
            'email' => $event->rawData->get('email'),
            'phone' => $event->rawData->get('phone'),
        ]);

        return $customerRecord;
    }

    public function onUpdate(WebhookReceived $event)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop($event)->customers()
            ->where('meta->marketplace_customer_id', $event->rawData->get('id'))
            ->first();

        if (!$customerRecord) {
            return null;
        }

        $customerRecord->updateContact([
            'first_name' => $event->rawData->get('first_name'),
            'last_name' => $event->rawData->get('last_name'),
            'email' => $event->rawData->get('email'),
            'phone' => $event->rawData->get('phone'),
        ]);

        return $customerRecord;
    }

    public function onDelete(WebhookReceived $event)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop($event)->customers()
            ->where('meta->marketplace_customer_id', $event->rawData->get('id'))
            ->first();

        if ($customerRecord) {
            $customerRecord->delete();
        }

        return $customerRecord;
    }

    public function processFor(): string
    {
        return Customer::class;
    }
}
