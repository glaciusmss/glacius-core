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
use App\Enums\Shopify\WebhookTopic;
use App\Events\Webhook\CustomerWebhookReceivedFromMarketplace;
use App\Events\Webhook\OrderWebhookReceivedFromMarketplace;
use App\Services\BaseMarketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

class Webhook extends BaseMarketplace implements WebhookContract
{
    protected $sdkFactory;

    public function __construct(SdkFactory $sdkFactory)
    {
        $this->sdkFactory = $sdkFactory;
    }

    public function register()
    {
        $webhookIds = [];
        $topics = WebhookTopic::getValues();

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

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, $this->getConfig('secret'), true));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function dispatcher(Request $request)
    {
        $topic = $request->headers->get('x-shopify-topic');
        $shopDomain = $request->headers->get('x-shopify-shop-domain');
        $rawData = collect($request->all())->merge(['shop_domain' => $shopDomain]);

        if (Str::startsWith($topic, 'orders/')) {
            event(new OrderWebhookReceivedFromMarketplace($topic, $rawData, $this->name()));
        } else if (Str::startsWith($topic, 'customers/')) {
            event(new CustomerWebhookReceivedFromMarketplace($topic, $rawData, $this->name()));
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
                    'address' => $this->getConfig('webhook_url'),
                    'format' => 'json',
                ]);

            return $response['id'];
        } catch (ApiException $exception) {
            //ignore this if already created
            return null;
        }
    }
}
