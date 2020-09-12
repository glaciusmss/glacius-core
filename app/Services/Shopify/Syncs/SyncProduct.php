<?php


namespace App\Services\Shopify\Syncs;


use App\Contracts\Sync;
use App\DTO\SyncState;
use App\Enums\State;
use App\Events\SyncEvent;
use App\MarketplaceIntegration;
use App\Product;
use App\Services\Shopify\Helpers\HasSdk;
use App\Utils\FilterInternalField;
use App\Utils\Helper;
use Illuminate\Support\Arr;
use PHPShopify\Exception\ApiException;

class SyncProduct implements Sync
{
    use HasSdk, FilterInternalField;

    public function onCreate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState
    {
        /** @var Product $product */
        $product = $event->model;

        $createData = $this->prepareCreateData($product);

        $sdk = $this->getSdk([
            'AccessToken' => $marketplaceIntegration->token,
            'ShopUrl' => $marketplaceIntegration->meta['shopify_shop']
        ]);

        try {
            $response = $sdk->Product->post($createData);

            //update model with shopify meta
            $product->update([
                'meta->shopify_product_id' => $response['id']
            ]);

            $product->productVariants
                ->find($product->productVariants->first()['id'])
                ->update([
                    'meta->shopify_variant_id' => Arr::get($response, 'variants.0.id')
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

        $updateData = $this->prepareUpdateData($product);

        $sdk = $this->getSdk([
            'AccessToken' => $marketplaceIntegration->token,
            'ShopUrl' => $marketplaceIntegration->meta['shopify_shop']
        ]);

        try {
            $response = $sdk->Product($product->meta['shopify_product_id'])->put($updateData);

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
            'AccessToken' => $marketplaceIntegration->token,
            'ShopUrl' => $marketplaceIntegration->meta['shopify_shop']
        ]);

        try {
            $sdk->Product($product->meta['shopify_product_id'])->delete();

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
        /** @var Product $product */
        $newVariants = $product->productVariants->first();

        $images = [];
        $product->getMedia()->each(function ($item) use (&$images) {
            $images[] = ['src' => $item->getFullUrl()];
        });

        return [
            'title' => $product->name,
            'body_html' => $product->description,
            'images' => $images,
            'variants' => [
                [
                    'title' => 'default',
                    'price' => $newVariants['price'],
                    'inventory_quantity' => $newVariants['stock']
                ]
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

            $updateData['variants'][] = Arr::add($filteredProductVariant, 'id', $productVariant->meta['shopify_variant_id']);
        }

        $product->getMedia()->each(function ($item) use (&$updateData) {
            $updateData['images'][] = ['src' => $item->getFullUrl()];
        });

        $filteredProduct = Helper::transformArrayKey(
            $this->filterInternalProductField($product),
            ['name' => 'title', 'description' => 'body_html']
        );

        $updateData = array_merge($updateData, $filteredProduct);

        return Arr::add($updateData, 'id', $product->meta['shopify_product_id']);
    }
}
