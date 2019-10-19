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
use App\Order;
use App\Product;
use App\Services\BaseProcessor;
use Illuminate\Support\Collection;

class OrderProcessor extends BaseProcessor
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
        if (WebhookTopic::OrderCreate()->is($this->event->topic)) {
            return EventType::Created();
        }

        return null;
    }

    protected function processWhenCreated(Collection $rawData)
    {
        //easystore prefixed with order
        $rawData = collect($rawData['order']);

        /** @var Order $orderRecord */
        $orderRecord = $this->getShop()->orders()->create([
            'total_price' => $rawData->get('total_price'),
            'subtotal_price' => $rawData->get('subtotal_price'),
            'meta' => ['marketplace_order_id' => $rawData->get('id')],
            'marketplace_id' => $this->getMarketplace()->id
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
                $this->transformAddressAttr($billingAddress, ['province' => 'state'])
            );
        }

        if ($shippingAddress = $rawData->get('shipping_address')) {
            $this->createShippingAddress(
                $orderRecord,
                $this->transformAddressAttr($shippingAddress, ['province' => 'state'])
            );
        }

        if ($customerData = $rawData->get('customer')) {
            $customer = Customer::whereMarketplaceId($this->getMarketplace()->id)
                ->where('meta->marketplace_customer_id', $customerData['id'])
                ->first();

            if ($customer) {
                $orderRecord->customer()->associate($customer);
                $orderRecord->save();
            }
        }

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
