<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/21/2019
 * Time: 5:41 PM.
 */

namespace App\Services\Woocommerce\Syncs;


use App\Contracts\SdkFactory;
use App\Contracts\Sync as SyncContract;
use App\DTO\SyncState;
use App\Enums\MarketplaceEnum;
use App\Enums\State;
use App\MarketplaceIntegration;
use App\Product;
use App\Services\BaseMarketplace;
use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

class SyncProduct extends BaseMarketplace implements SyncContract
{
    protected $sdkFactory;
    protected $marketplaceIntegration;
    /* @var LoggerInterface $logger */
    protected $logger;

    public function __construct($config, CacheContract $cache, SdkFactory $sdkFactory)
    {
        parent::__construct($config, $cache);

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
            $sdk->post(
                'products',
                $createData
            );

            $response = json_decode($sdk->http->getResponse()->getBody(), true);

            $this->logger->info('create response: ' . json_encode($response));

            //update model with woocommerce meta
            $model->update([
                'meta' => array_merge(Arr::wrap($model->meta), ['woocommerce_product_id' => $response['id']])
            ]);

            return new SyncState(State::Success());
        } catch (HttpClientException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function whenUpdated(Model $model)
    {
        $sdk = $this->setupAndGetSdk();
        $woocommerceProductId = $model->meta['woocommerce_product_id'];

        $updateData = $this->prepareUpdateData($model);

        $this->logger->info('update data: ' . json_encode($updateData));

        try {
            $sdk->put(
                'products/' . $woocommerceProductId,
                $updateData
            );

            $response = json_decode($sdk->http->getResponse()->getBody(), true);

            $this->logger->info('update response: ' . json_encode($response));

            return new SyncState(State::Success());
        } catch (HttpClientException $ex) {
            return new SyncState(State::Error(), $ex->getMessage());
        }
    }

    public function whenDeleted(Model $model)
    {
        $sdk = $this->setupAndGetSdk();

        try {
            $sdk->delete(
                'products/' . $model->meta['woocommerce_product_id'],
                ['force' => true]
            );

            return new SyncState(State::Success());
        } catch (HttpClientException $ex) {
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
        return MarketplaceEnum::WooCommerce();
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
            'name' => $model->name,
            'description' => $model->description,
            'regular_price' => $newVariants['price'],
            'stock_quantity' => $newVariants['stock'],
            'images' => $images,
        ];
    }

    protected function prepareUpdateData(Model $model)
    {
        $updateData = [];

        /** @var Product $model */
        foreach ($model->productVariants as $productVariant) {
            $productVariantChanges = Arr::except($productVariant->getChanges(), ['created_at', 'updated_at', 'deleted_at']);
            foreach ($productVariantChanges as $key => $value) {
                switch ($key) {
                    case 'stock':
                        $updateData['stock_quantity'] = $value;
                        break;
                    case 'price':
                        $updateData['regular_price'] = $value;
                        break;
                    default:
                        $updateData[$key] = $value;
                        break;
                }
            }
        }

        $updateData['images'] = [];
        $model->getMedia()->each(function ($item) use (&$updateData) {
            $updateData['images'][] = ['src' => $item->getFullUrl()];
        });

        $productChanges = Arr::except($model->getChanges(), ['created_at', 'updated_at', 'deleted_at']);

        foreach ($productChanges as $key => $value) {
            switch ($key) {
                default:
                    $updateData[$key] = $value;
                    break;
            }
        }

        return $updateData;
    }

    /**
     * @return Client
     */
    protected function setupAndGetSdk()
    {
        $this->sdkFactory->setupSdk(null, [
            'url' => $this->marketplaceIntegration->meta['woocommerce_store_url'],
            'consumer_key' => $this->marketplaceIntegration->meta['key'],
            'consumer_secret' => $this->marketplaceIntegration->meta['secret']
        ]);

        return $this->sdkFactory->getSdk();
    }
}
