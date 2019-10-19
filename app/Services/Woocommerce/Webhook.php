<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 2:50 PM.
 */

namespace App\Services\Woocommerce;


use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\MarketplaceEnum;
use App\Enums\Woocommerce\WebhookTopic;
use App\Events\Webhook\CustomerWebhookReceivedFromMarketplace;
use App\Events\Webhook\OrderWebhookReceivedFromMarketplace;
use App\Services\BaseMarketplace;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Webhook extends BaseMarketplace implements WebhookContract
{
    protected $sdkFactory;

    public function __construct(SdkFactory $sdkFactory)
    {
        $this->sdkFactory = $sdkFactory;
    }

    public function register()
    {
        /** @var Client $sdk */
        $sdk = $this->sdkFactory->getSdk();
        $topics = WebhookTopic::getValues();
        $webhookId = [];
        $createParam = [];

        foreach ($topics as $topic) {
            $createParam[] = $this->createWebhook($topic);
        }

        try {
            $sdk->post('webhooks/batch', [
                'create' => $createParam
            ]);

            $response = json_decode($sdk->http->getResponse()->getBody(), true);

            foreach ($response['create'] as $createdWebhook) {
                $webhookId[] = $createdWebhook['id'];
            }
        } catch (\Exception $exception) {
            //ignore this if already created
        }

        return $webhookId;
    }

    public function validateHmac(Request $request)
    {
        $hmacHeader = $request->headers->get('x-wc-webhook-signature', '');
        $data = file_get_contents('php://input');

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, $this->getConfig('secret'), true));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function dispatcher(Request $request)
    {
        $topic = $request->headers->get('x-wc-webhook-topic');
        $woocommerceStoreUrl = $request->headers->get('x-wc-webhook-source');
        $rawData = collect($request->all())->merge(['woocommerce_store_url' => $woocommerceStoreUrl]);

        if (Str::startsWith($topic, 'order.')) {
            event(new OrderWebhookReceivedFromMarketplace($topic, $rawData, $this->name()));
        } else if (Str::startsWith($topic, 'customer.')) {
            event(new CustomerWebhookReceivedFromMarketplace($topic, $rawData, $this->name()));
        }
    }

    public function name()
    {
        return MarketplaceEnum::WooCommerce();
    }

    protected function createWebhook($topic)
    {
        return [
            'name' => 'GlaciusMSS',
            'topic' => $topic,
            'delivery_url' => $this->getConfig('webhook_url'),
            'secret' => $this->getConfig('secret'),
        ];
    }
}
