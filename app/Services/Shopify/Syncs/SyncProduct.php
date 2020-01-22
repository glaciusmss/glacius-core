<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/21/2019
 * Time: 5:41 PM.
 */

namespace App\Services\Shopify\Syncs;


use App\Contracts\SdkFactory;
use App\DTO\SyncState;
use App\Enums\MarketplaceEnum;
use App\Enums\State;
use App\Product;
use App\Services\BaseSync;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

class SyncProduct extends BaseSync
{
    protected $sdkFactory;

    public function __construct(SdkFactory $sdkFactory)
    {
        $this->sdkFactory = $sdkFactory;
    }

    public function whenCreated(Model $model)
    {
        /** @var Product $model */
        $sdk = $this->setupAndGetSdk();

        $createData = $this->prepareCreateData($model);

        $this->log('create data', $createData);

        try {
            $response = $sdk->Product->post(
                $createData
            );

            $this->log('create response', $response);

            //update model with shopify meta
            $model->update([
                'meta' => array_merge(Arr::wrap($model->meta), ['shopify_product_id' => $response['id']])
            ]);

            //reload productVariants
            $model->unsetRelation('productVariants');
            $model->productVariants
                ->find($model->productVariants->first()['id'])
                ->update([
                    'meta' => array_merge(Arr::wrap($model->productVariants->first()['meta']), ['shopify_variant_id' => Arr::get($response, 'variants.0.id')])
                ]);

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function whenUpdated(Model $model)
    {
        $sdk = $this->setupAndGetSdk();
        $shopifyProductid = $model->meta['shopify_product_id'];

        $updateData = $this->prepareUpdateData($model);

        $this->log('update data', $updateData);

        try {
            $response = $sdk->Product($shopifyProductid)->put(
                $updateData
            );

            $this->log('update response', $response);

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function whenDeleted(Model $model)
    {
        $sdk = $this->setupAndGetSdk();

        try {
            $sdk->Product(
                $model->meta['shopify_product_id']
            )->delete();

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function withExisting()
    {
        //TODO: Implement withExisting() method.
    }

    public function name()
    {
        return MarketplaceEnum::Shopify();
    }

    protected function syncFor()
    {
        return Product::class;
    }

    protected function prepareCreateData(Model $model)
    {
        /** @var Product $model */
        $newVariants = $model->productVariants->first();

        $images = [];
        $model->getMedia()->each(function ($item) use (&$images) {
            $images[] = ['src' => $item->getFullUrl()];
        });

        return [
            'title' => $model->name,
            'body_html' => $model->description,
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

    protected function prepareUpdateData(Model $model)
    {
        $updateData = [];

        /** @var Product $model */
        foreach ($model->productVariants as $productVariant) {
            $productVariantChanges = Arr::except($productVariant->getChanges(), ['created_at', 'updated_at', 'deleted_at']);
            foreach ($productVariantChanges as $key => $value) {
                $updateProductVariantData = [];
                switch ($key) {
                    case 'stock':
                        $updateProductVariantData['inventory_quantity'] = $value;
                        break;
                    default:
                        $updateProductVariantData[$key] = $value;
                        break;
                }

                if (!isset($updateData['variants']) || !is_array($updateData['variants'])) {
                    $updateData['variants'] = [];
                }

                $updateData['variants'][] = Arr::add($updateProductVariantData, 'id', $productVariant->meta['shopify_variant_id']);
            }
        }

        $updateData['images'] = [];
        $model->getMedia()->each(function ($item) use (&$updateData) {
            $updateData['images'][] = ['src' => $item->getFullUrl()];
        });

        $productChanges = Arr::except($model->getChanges(), ['created_at', 'updated_at', 'deleted_at']);

        foreach ($productChanges as $key => $value) {
            switch ($key) {
                case 'name':
                    $updateData['title'] = $value;
                    break;
                case 'description':
                    $updateData['body_html'] = $value;
                    break;
                default:
                    $updateData[$key] = $value;
                    break;
            }
        }

        return Arr::add($updateData, 'id', $model->meta['shopify_product_id']);
    }

    /**
     * @return ShopifySDK
     */
    protected function setupAndGetSdk()
    {
        $this->sdkFactory->setupSdk(null, [
            'AccessToken' => $this->marketplaceIntegration->token,
            'shopifyShop' => $this->marketplaceIntegration->meta['shopify_shop']
        ]);

        return $this->sdkFactory->getSdk();
    }
}
