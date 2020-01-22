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
use App\Order;
use App\Product;
use App\Services\BaseProcessor;
use App\Utils\Helper;
use Illuminate\Support\Collection;

class OrderProcessor extends BaseProcessor
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

    protected function getEventType()
    {
        if (WebhookTopic::OrderCreate()->is($this->event->topic)) {
            return EventType::Created();
        }

        return null;
    }

    protected function processFor()
    {
        return Order::class;
    }

    protected function processWhenCreated(Collection $rawData)
    {
        /** @var Order $orderRecord */
        $orderRecord = $this->getShop()->orders()->create([
            'total_price' => $rawData->get('total'),
            'subtotal_price' => $rawData->get('total'),
            'meta' => ['marketplace_order_id' => $rawData->get('id')],
            'marketplace_id' => $this->getMarketplace()->id
        ]);

        foreach ($rawData->get('line_items') as $lineItem) {
            $product = Product::where('meta->woocommerce_product_id', $lineItem['product_id'])
                ->first();

            if ($product) {
                $orderRecord->products()->attach($product, [
                    'quantity' => $lineItem['quantity']
                ]);
            }
        }

        if ($billingAddress = $rawData->get('billing')) {
            $this->createBillingAddress(
                $orderRecord,
                Helper::transformArrayKey($billingAddress, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
            );
        }

        if ($shippingAddress = $rawData->get('shipping')) {
            $this->createShippingAddress(
                $orderRecord,
                Helper::transformArrayKey($shippingAddress, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
            );
        }

        if ($customerId = $rawData->get('customer_id')) {
            $customer = Customer::whereMarketplaceId($this->getMarketplace()->id)
                ->where('meta->marketplace_customer_id', $customerId)
                ->first();

            if ($customer) {
                $orderRecord->customer()->associate($customer);
                $orderRecord->save();
            }
        }

        $this->log('created order record', $orderRecord->toArray());

        return $orderRecord;
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
