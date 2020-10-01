<?php

namespace App\Services\Easystore\Syncs;

use App\Contracts\Sync;
use App\DTO\SyncState;
use App\Enums\State;
use App\Events\SyncEvent;
use App\Models\MarketplaceIntegration;
use App\Models\Product;
use App\Services\Easystore\Helpers\HasSdk;
use App\Utils\FilterInternalField;
use App\Utils\Helper;
use EasyStore\Exception\ApiException;
use Illuminate\Support\Arr;

class SyncProduct implements Sync
{
    use HasSdk, FilterInternalField;

    public function onCreate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $createData = $this->prepareCreateData($product);

        $sdk = $this->getSdk([
            'shop' => $marketplaceIntegration->meta['easystore_shop'],
            'access_token' => $marketplaceIntegration->token,
        ]);

        try {
            $response = $sdk->post('/products.json', ['product' => $createData]);

            //update model with easystore meta
            $product->update([
                'meta->easystore_product_id' => Arr::get($response, 'product.id'),
            ]);

            $product->productVariants
                ->find($product->productVariants->first()['id'])
                ->update([
                    'meta->easystore_variant_id' => Arr::get($response, 'product.variants.0.id'),
                ]);

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function onUpdate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $easystoreProductId = $product->meta['easystore_product_id'];

        $updateData = $this->prepareUpdateData($product);

        $sdk = $this->getSdk([
            'shop' => $marketplaceIntegration->meta['easystore_shop'],
            'access_token' => $marketplaceIntegration->token,
        ]);

        try {
            $variantsData = Arr::pull($updateData, 'variants');
            $response = $sdk->put("/products/{$easystoreProductId}.json", ['product' => $updateData]);

            foreach (Arr::wrap($variantsData) as $variantData) {
                $variantsId = Arr::pull($variantData, 'id');
                $response = $sdk->put("/products/{$easystoreProductId}/variants/{$variantsId}.json", ['variant' => $variantData]);
            }

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function onDelete(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $sdk = $this->getSdk([
            'shop' => $marketplaceIntegration->meta['easystore_shop'],
            'access_token' => $marketplaceIntegration->token,
        ]);

        $easystoreProductId = $product->meta['easystore_product_id'];

        try {
            $sdk->delete("/products/{$easystoreProductId}.json");

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
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
            $images[] = ['url' => $item->getFullUrl()];
        });

        return [
            'name' => $product->name,
            'body_html' => $product->description,
            'inventory_management' => 'none',
            'published_at' => now()->toDateTimeString(),
            'images' => $images,
            'variants' => [
                [
                    'price' => $newVariants['price'],
                    'inventory_quantity' => $newVariants['stock'],
                ],
            ],
        ];
    }

    protected function prepareUpdateData(Product $product)
    {
        $updateData = [
            'variants' => [],
            'images' => [],
        ];

        foreach ($product->productVariants as $productVariant) {
            $filteredProductVariant = Helper::transformArrayKey(
                $this->filterInternalProductVariantField($productVariant),
                ['stock' => 'inventory_quantity']
            );

            $updateData['variants'][] = Arr::add($filteredProductVariant, 'id', $productVariant->meta['easystore_variant_id']);
        }

        $product->getMedia()->each(function ($item) use (&$updateData) {
            $updateData['images'][] = ['url' => $item->getFullUrl()];
        });

        $filteredProduct = Helper::transformArrayKey(
            $this->filterInternalProductField($product),
            ['description' => 'body_html']
        );

        return array_merge($updateData, $filteredProduct);
    }
}
