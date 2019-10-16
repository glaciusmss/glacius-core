<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 2:50 PM.
 */

namespace App\Services\Shopify;


use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\MarketplaceEnum;
use App\Events\Webhook\CustomerCreateReceivedFromMarketplace;
use App\Events\Webhook\OrderCreateReceivedFromMarketplace;
use App\Services\BaseMarketplace;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

class Webhook extends BaseMarketplace implements WebhookContract
{
    protected $sdkFactory;

    public function __construct($config, CacheContract $cache, SdkFactory $sdkFactory)
    {
        parent::__construct($config, $cache);

        $this->sdkFactory = $sdkFactory;
    }

    public function register()
    {
        $webhookIds = [];
        $topics = ['orders/create', 'customers/create'];

        foreach ($topics as $topic) {
            $webhookId = $this->createWebhook($topic);
            if ($webhookId !== null) {
                $webhookIds[] = $webhookId;
            }
        }

        return $webhookIds;
    }

    public function validateHmac(Request $request)
    {
        $hmacHeader = $request->headers->get('x-shopify-hmac-sha256', '');
        $data = file_get_contents('php://input');

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, $this->config['secret'], true));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function dispatcher(Request $request)
    {
        $topic = $request->headers->get('x-shopify-topic');
        $shopDomain = $request->headers->get('x-shopify-shop-domain');
        $rawData = collect($request->all())->merge(['shop_domain' => $shopDomain]);

        if ($topic === 'orders/create') {
            event(new OrderCreateReceivedFromMarketplace($rawData, $this->name()));
        } else if ($topic === 'customers/create') {
            event(new CustomerCreateReceivedFromMarketplace($rawData, $this->name()));
        }
    }

    public function name()
    {
        return MarketplaceEnum::Shopify();
    }

    protected function createWebhook($topic)
    {
        /** @var ShopifySDK $sdk */
        $sdk = $this->sdkFactory->getSdk();

        try {
            $response = $sdk->Webhook
                ->post([
                    'topic' => $topic,
                    'address' => $this->config['webhook_url'],
                    'format' => 'json',
                ]);

            return $response['id'];
        } catch (ApiException $exception) {
            //ignore this if already created
            return null;
        }
    }
}
