<?php

namespace App\Services\Easystore\Processors;

use App\Contracts\Processor;
use App\Customer;
use App\Events\Webhook\WebhookReceived;
use App\Utils\Helper;

class CustomerProcessor extends BaseProcessor implements Processor
{
    public function onCreate(WebhookReceived $event)
    {
        /** @var Customer $customerRecord */
        $customerRecord = $this->getShop($event)->customers()->create([
            'meta' => ['marketplace_customer_id' => $event->rawData->get('customer_id')],
            'marketplace_id' => $this->getMarketplace($event->identifier)->id,
        ]);

        $customerRecord->addContact([
            'first_name' => $event->rawData->get('first_name'),
            'last_name' => $event->rawData->get('last_name'),
            'email' => $event->rawData->get('email'),
            'phone' => $event->rawData->get('phone'),
        ]);

        if ($addresses = $event->rawData->get('address')) {
            foreach ($addresses as $address) {
                $this->createAddress(
                    $customerRecord,
                    Helper::transformArrayKey($address, ['province' => 'state'])
                );
            }
        }

        return $customerRecord;
    }

    public function onUpdate(WebhookReceived $event)
    {
        // TODO: Implement onUpdate() method.
    }

    public function onDelete(WebhookReceived $event)
    {
        // TODO: Implement onDelete() method.
    }

    public function processFor(): string
    {
        return Customer::class;
    }
}
