<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/21/2019
 * Time: 5:41 PM.
 */

namespace App\Services\Easystore\Syncs;


use App\Contracts\SdkFactory;
use App\Contracts\Sync as SyncContract;
use App\DTO\SyncState;
use App\Enums\MarketplaceEnum;
use App\Enums\State;
use App\MarketplaceIntegration;
use App\Product;
use App\Services\BaseMarketplace;
use EasyStore\Exception\ApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

class SyncProduct extends BaseMarketplace implements SyncContract
{
    protected $sdkFactory;
    protected $marketplaceIntegration;
    /* @var LoggerInterface $logger */
    protected $logger;

    public function __construct(SdkFactory $sdkFactory)
    {
        $this->sdkFactory = $sdkFactory;
        $this->logger = \Log::channel('sync_product');
    }

    public function whenCreated(Model $model)
    {
        /** @var Product $model */
        $sdk = $this->setupAndGetSdk();

        $createData = $this->prepareCreateData($model);

        $this->logger->info('create data: ' . json_encode($createData));

        try {
            $response = $sdk->post('/products.json', ['product' => $createData]);

            $this->logger->info('create response: ' . json_encode($response));

            //update model with easystore meta
            $model->update([
                'meta' => array_merge(Arr::wrap($model->meta), ['easystore_product_id' => Arr::get($response, 'product.id')])
            ]);

            //reload productVariants
            $model->unsetRelation('productVariants');
            $model->productVariants
                ->find($model->productVariants->first()['id'])
                ->update([
                    'meta' => array_merge(Arr::wrap($model->productVariants->first()['meta']), ['easystore_variant_id' => Arr::get($response, 'product.variants.0.id')])
                ]);

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function whenUpdated(Model $model)
    {
        $sdk = $this->setupAndGetSdk();
        $easystoreProductId = $model->meta['easystore_product_id'];

        $updateData = $this->prepareUpdateData($model);

        $this->logger->info('update data: ' . json_encode($updateData));

        try {
            $variantsData = Arr::pull($updateData, 'variants');
            $response = $sdk->put("/products/{$easystoreProductId}.json", ['product' => $updateData]);

            $this->logger->info('update response: ' . json_encode($response));

            $this->logger->info('variants data: ' . json_encode($variantsData));

            foreach (Arr::wrap($variantsData) as $variantData) {
                $variantsId = Arr::pull($variantData, 'id');
                $response = $sdk->put("/products/{$easystoreProductId}/variants/{$variantsId}.json", ['variant' => $variantData]);
                $this->logger->info('update variants response: ' . json_encode($response));
            }

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function whenDeleted(Model $model)
    {
        $sdk = $this->setupAndGetSdk();

        $easystoreProductId = $model->meta['easystore_product_id'];

        try {
            $sdk->delete("/products/{$easystoreProductId}.json");

            return new SyncState(State::Success());
        } catch (ApiException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function withExisting()
    {
        //TODO: Implement withExisting() method.
    }

    public function setMarketplaceIntegration(MarketplaceIntegration $marketplaceIntegration)
    {
        $this->marketplaceIntegration = $marketplaceIntegration;
        return $this;
    }

    public function name()
    {
        return MarketplaceEnum::EasyStore();
    }

    protected function prepareCreateData(Model $model)
    {
        /** @var Product $model */
        $newVariants = $model->productVariants->first();

        $images = [];
        $model->getMedia()->each(function ($item) use (&$images) {
            $images[] = ['url' => $item->getFullUrl()];
        });

        return [
            'name' => $model->name,
            'body_html' => $model->description,
            "inventory_management" => 'none',
            'published_at' => now()->toDateTimeString(),
            'images' => $images,
            'variants' => [
                [
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

                $updateData['variants'][] = Arr::add($updateProductVariantData, 'id', $productVariant->meta['easystore_variant_id']);
            }
        }

        $updateData['images'] = [];
        $model->getMedia()->each(function ($item) use (&$updateData) {
            $updateData['images'][] = ['url' => $item->getFullUrl()];
        });

        $productChanges = Arr::except($model->getChanges(), ['created_at', 'updated_at', 'deleted_at']);

        foreach ($productChanges as $key => $value) {
            switch ($key) {
                case 'name':
                    $updateData['name'] = $value;
                    break;
                case 'description':
                    $updateData['body_html'] = $value;
                    break;
                default:
                    $updateData[$key] = $value;
                    break;
            }
        }

        return $updateData;
    }

    /**
     * @return \EasyStore\Client
     */
    protected function setupAndGetSdk()
    {
        $this->sdkFactory->setupSdk(null, [
            'access_token' => $this->marketplaceIntegration->token,
            'shop' => $this->marketplaceIntegration->meta['easystore_shop'],
        ]);

        return $this->sdkFactory->getSdk();
    }
}
