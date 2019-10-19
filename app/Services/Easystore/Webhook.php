<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 2:50 PM.
 */

namespace App\Services\Easystore;


use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\Easystore\WebhookTopic;
use App\Enums\MarketplaceEnum;
use App\Events\Webhook\CustomerWebhookReceivedFromMarketplace;
use App\Events\Webhook\OrderWebhookReceivedFromMarketplace;
use App\Services\BaseMarketplace;
use EasyStore\Exception\ApiException;
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
        $hmacHeader = $request->headers->get('easystore-hmac-sha256', '');
        $data = file_get_contents('php://input');

        $calculatedHmac = hash_hmac('sha256', $data, $this->getConfig('secret'));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function dispatcher(Request $request)
    {
        $topic = $request->headers->get('easystore-topic');
        $shopDomain = $request->headers->get('easystore-shop-domain');
        $rawData = collect($request->all())->merge(['shop_domain' => $shopDomain]);

        if (Str::startsWith($topic, 'order/')) {
            event(new OrderWebhookReceivedFromMarketplace($topic, $rawData, $this->name()));
        } else if (Str::startsWith($topic, 'customer/')) {
            event(new CustomerWebhookReceivedFromMarketplace($topic, $rawData, $this->name()));
        }
    }

    public function name()
    {
        return MarketplaceEnum::EasyStore();
    }

    protected function createWebhook($topic)
    {
        /** @var \EasyStore\Client $sdk */
        $sdk = $this->sdkFactory->getSdk();

        try {
            $response = $sdk->post('/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'url' => $this->getConfig('webhook_url'),
                ]
            ]);

            return $response['id'];
        } catch (ApiException $exception) {
            //ignore this if already created
            return null;
        }
    }
}
