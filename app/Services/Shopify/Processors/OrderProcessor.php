<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/20/2019
 * Time: 10:09 AM.
 */

namespace App\Services\Shopify\Processors;


use App\Address;
use App\Enums\AddressType;
use App\Enums\EventType;
use App\Enums\MarketplaceEnum;
use App\Events\Webhook\OrderCreateReceivedFromMarketplace;
use App\Order;
use App\Product;
use App\Services\BaseProcessor;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class OrderProcessor extends BaseProcessor
{
    public function name()
    {
        return MarketplaceEnum::Shopify();
    }

    public function getShop($withRelations = null)
    {
        if ($this->shop) {
            return $this->shop;
        }

        return $this->shop = $this->getMarketplace()
            ->shops()
            ->wherePivot('meta->shopify_shop', '=', $this->event->rawData->get('shop_domain'))
            ->first();
    }

    protected function getEventType()
    {
        if ($this->event instanceof OrderCreateReceivedFromMarketplace) {
            return EventType::Created();
        }

        return null;
    }

    protected function processWhenCreated(Collection $rawData)
    {
        /** @var Order $orderRecord */
        $orderRecord = $this->getShop()->orders()->create([
            'total_price' => $rawData->get('total_price'),
            'subtotal_price' => $rawData->get('subtotal_price'),
            'meta' => ['marketplace_order_id' => $rawData->get('id')],
            'marketplace_id' => $this->getMarketplace()->id
        ]);

        foreach ($rawData->get('line_items') as $lineItem) {
            $product = Product::where('meta->shopify_product_id', '=', $lineItem['product_id'])
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
    }

    protected function processWhenUpdated(Collection $rawData)
    {
        // TODO: Implement processWhenUpdated() method.
    }
}
