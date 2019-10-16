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
use App\Events\Webhook\CustomerCreateReceivedFromMarketplace;
use App\Events\Webhook\OrderCreateReceivedFromMarketplace;
use App\Services\BaseMarketplace;
use Automattic\WooCommerce\Client;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;

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
        /** @var Client $sdk */
        $sdk = $this->sdkFactory->getSdk();
        $webhookId = [];

        try {
            $sdk->post('webhooks/batch', [
                'create' => [
                    [
                        'name' => 'GlaciusMSS',
                        'topic' => 'order.created',
                        'delivery_url' => $this->config['webhook_url'],
                        'secret' => $this->config['secret'],
                    ],
                    [
                        'name' => 'GlaciusMSS',
                        'topic' => 'customer.created',
                        'delivery_url' => $this->config['webhook_url'],
                        'secret' => $this->config['secret'],
                    ],
                ]
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

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, $this->config['secret'], true));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function dispatcher(Request $request)
    {
        $topic = $request->headers->get('x-wc-webhook-topic');
        $woocommerceStoreUrl = $request->headers->get('x-wc-webhook-source');
        $rawData = collect($request->all())->merge(['woocommerce_store_url' => $woocommerceStoreUrl]);

        if ($topic === 'order.created') {
            event(new OrderCreateReceivedFromMarketplace($rawData, $this->name()));
        } else if ($topic === 'customer.created') {
            event(new CustomerCreateReceivedFromMarketplace($rawData, $this->name()));
        }
    }

    public function name()
    {
        return MarketplaceEnum::WooCommerce();
    }
}
