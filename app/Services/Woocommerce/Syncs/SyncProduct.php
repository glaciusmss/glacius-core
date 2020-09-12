<?php


namespace App\Services\Woocommerce\Syncs;


use App\Contracts\Sync;
use App\DTO\SyncState;
use App\Enums\State;
use App\Events\SyncEvent;
use App\MarketplaceIntegration;
use App\Product;
use App\Services\Woocommerce\Helpers\HasSdk;
use App\Utils\FilterInternalField;
use App\Utils\Helper;

class SyncProduct implements Sync
{
    use HasSdk, FilterInternalField;

    public function onCreate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $createData = $this->prepareCreateData($product);

        $sdk = $this->getSdk([
            'woocommerceStoreUrl' => $marketplaceIntegration->meta['woocommerce_store_url'],
            'consumerKey' => $marketplaceIntegration->meta['key'],
            'consumerSecret' => $marketplaceIntegration->meta['secret'],
        ]);

        try {
            $sdk->post('products', $createData);

            $response = json_decode($sdk->http->getResponse()->getBody(), true);

            //update model with woocommerce meta
            $product->update([
                'meta->woocommerce_product_id' => $response['id']
            ]);

            return new SyncState(State::Success());
        } catch (\Throwable $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function onUpdate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $sdk = $this->getSdk([
            'woocommerceStoreUrl' => $marketplaceIntegration->meta['woocommerce_store_url'],
            'consumerKey' => $marketplaceIntegration->meta['key'],
            'consumerSecret' => $marketplaceIntegration->meta['secret'],
        ]);

        $woocommerceProductId = $product->meta['woocommerce_product_id'];

        $updateData = $this->prepareUpdateData($product);

        try {
            $sdk->put('products/' . $woocommerceProductId, $updateData);

            $response = json_decode($sdk->http->getResponse()->getBody(), true);

            return new SyncState(State::Success());
        } catch (\Throwable $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function onDelete(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $sdk = $this->getSdk([
            'woocommerceStoreUrl' => $marketplaceIntegration->meta['woocommerce_store_url'],
            'consumerKey' => $marketplaceIntegration->meta['key'],
            'consumerSecret' => $marketplaceIntegration->meta['secret'],
        ]);

        try {
            $sdk->delete(
                'products/' . $product->meta['woocommerce_product_id'],
                ['force' => true]
            );

            return new SyncState(State::Success());
        } catch (\Throwable $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function processFor(): string
    {
        return Product::class;
    }

    public function withExisting()
    {
        // TODO: Implement withExisting() method.
    }

    protected function prepareCreateData(Product $product)
    {
        $newVariants = $product->productVariants->first();

        $images = [];
        $product->getMedia()->each(function ($item) use (&$images) {
            $images[] = ['src' => $item->getFullUrl()];
        });

        return [
            'name' => $product->name,
            'description' => $product->description,
            'regular_price' => $newVariants['price'],
            'stock_quantity' => $newVariants['stock'],
            'images' => $images,
        ];
    }

    protected function prepareUpdateData(Product $product)
    {
        $updateData = [
            'images' => []
        ];

        foreach ($product->productVariants as $productVariant) {
            $filteredProductVariant = Helper::transformArrayKey(
                $this->filterInternalProductVariantField($productVariant),
                ['stock' => 'stock_quantity', 'price' => 'regular_price']
            );

            array_merge($updateData, $filteredProductVariant);
        }

        $product->getMedia()->each(function ($item) use (&$updateData) {
            $updateData['images'][] = ['src' => $item->getFullUrl()];
        });

        $filteredProduct = Helper::transformArrayKey(
            $this->filterInternalProductField($product),
            []
        );

        return array_merge($updateData, $filteredProduct);
    }
}
