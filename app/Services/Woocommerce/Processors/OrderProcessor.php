<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/20/2019
 * Time: 10:09 AM.
 */

namespace App\Services\Woocommerce\Processors;


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
        if ($this->event instanceof OrderCreateReceivedFromMarketplace) {
            return EventType::Created();
        }

        return null;
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
            $product = Product::where('meta->woocommerce_product_id', '=', $lineItem['product_id'])
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
                $this->transformAddressAttr($billingAddress, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
            );
        }

        if ($shippingAddress = $rawData->get('shipping')) {
            $this->createShippingAddress(
                $orderRecord,
                $this->transformAddressAttr($shippingAddress, ['address_1' => 'address1', 'address_2' => 'address2', 'postcode' => 'zip'])
            );
        }
    }

    protected function processWhenUpdated(Collection $rawData)
    {
        // TODO: Implement processWhenUpdated() method.
    }
}
