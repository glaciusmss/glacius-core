<?php


namespace App\Services\Easystore\Processors;


use App\Contracts\Processor;
use App\Customer;
use App\Events\Webhook\WebhookReceived;
use App\Order;
use App\Product;
use App\Utils\Helper;

class OrderProcessor extends BaseProcessor implements Processor
{
    public function onCreate(WebhookReceived $event)
    {
        //easystore prefixed with order
        $rawData = collect($event->rawData['order']);

        /** @var Order $orderRecord */
        $orderRecord = $this->getShop($event)->orders()->create([
            'total_price' => $rawData->get('total_price'),
            'subtotal_price' => $rawData->get('subtotal_price'),
            'meta' => ['marketplace_order_id' => $rawData->get('id')],
            'marketplace_id' => $this->getMarketplace($event->identifier)->id
        ]);

        foreach ($rawData->get('line_items') as $lineItem) {
            $product = Product::where('meta->easystore_product_id', $lineItem['product_id'])
                ->first();

            if ($product) {
                $orderRecord->products()->attach($product, [
                    'quantity' => $lineItem['quantity']
                ]);
            }
        }

        if ($billingAddress = $rawData->get('billing_address')) {
            $this->createBillingAddress(
                $orderRecord,
                Helper::transformArrayKey($billingAddress, ['province' => 'state'])
            );
        }

        if ($shippingAddress = $rawData->get('shipping_address')) {
            $this->createShippingAddress(
                $orderRecord,
                Helper::transformArrayKey($shippingAddress, ['province' => 'state'])
            );
        }

        if ($customerData = $rawData->get('customer')) {
            $customer = Customer::whereMarketplaceId($this->getMarketplace($event->identifier)->id)
                ->where('meta->marketplace_customer_id', $customerData['id'])
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
