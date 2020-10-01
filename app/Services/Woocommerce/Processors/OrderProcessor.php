<?php

namespace App\Services\Woocommerce\Processors;

use App\Contracts\Processor;
use App\Models\Customer;
use App\Events\Webhook\WebhookReceived;
use App\Models\Order;
use App\Models\Product;
use App\Utils\Helper;

class OrderProcessor extends BaseProcessor implements Processor
{
    public function onCreate(WebhookReceived $event)
    {
        /** @var Order $orderRecord */
        $orderRecord = $this->getShop($event)->orders()->create([
            'total_price' => $event->rawData->get('total'),
            'subtotal_price' => $event->rawData->get('total'),
            'meta' => ['marketplace_order_id' => $event->rawData->get('id')],
            'marketplace_id' => $this->getMarketplace($event->identifier)->id,
        ]);

        foreach ($event->rawData->get('line_items') as $lineItem) {
            $product = Product::where('meta->woocommerce_product_id', $lineItem['product_id'])
                ->first();

            if ($product) {
                $orderRecord->products()->attach($product, [
                    'quantity' => $lineItem['quantity'],
                ]);
            }
        }

        if ($billingAddress = $event->rawData->get('billing')) {
            $this->createBillingAddress(
                $orderRecord,
                Helper::transformArrayKey($billingAddress, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
            );
        }

        if ($shippingAddress = $event->rawData->get('shipping')) {
            $this->createShippingAddress(
                $orderRecord,
                Helper::transformArrayKey($shippingAddress, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
            );
        }

        if ($customerId = $event->rawData->get('customer_id')) {
            $customer = Customer::whereMarketplaceId($this->getMarketplace($event->identifier)->id)
                ->where('meta->marketplace_customer_id', $customerId)
                ->first();

            if ($customer) {
                $orderRecord->customer()->associate($customer);
                $orderRecord->save();
            }
        }

        return $orderRecord;
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
        return Order::class;
    }
}
