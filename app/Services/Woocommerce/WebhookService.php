<?php

namespace App\Services\Woocommerce;

use App\Contracts\Webhook;
use App\MarketplaceIntegration;
use App\Services\Woocommerce\Enums\WebhookTopic;
use App\Services\Woocommerce\Helpers\HasSdk;
use Illuminate\Http\Request;
use Illuminate\Support\Enumerable;

class WebhookService implements Webhook
{
    use HasSdk;

    public function register(MarketplaceIntegration $marketplaceIntegration)
    {
        $topics = WebhookTopic::getValues();
        $webhookIds = [];
        $createParam = [];

        foreach ($topics as $topic) {
            $createParam[] = $this->createWebhook($topic);
        }

        $sdk = $this->getSdk([
            'woocommerceStoreUrl' => $marketplaceIntegration->meta['woocommerce_store_url'],
            'consumerKey' => $marketplaceIntegration->meta['key'],
            'consumerSecret' => $marketplaceIntegration->meta['secret'],
        ]);

        try {
            $sdk->post('webhooks/batch', [
                'create' => $createParam,
            ]);

            $response = json_decode($sdk->http->getResponse()->getBody(), true);

            foreach ($response['create'] as $createdWebhook) {
                $webhookIds[] = $createdWebhook['id'];
            }
        } catch (\Exception $exception) {
            //ignore this if already created
        }

        $marketplaceIntegration->update([
            'meta->webhook_id' => $webhookIds,
        ]);
    }

    public function remove(MarketplaceIntegration $marketplaceIntegration)
    {
        $sdk = $this->getSdk([
            'woocommerceStoreUrl' => $marketplaceIntegration->meta['woocommerce_store_url'],
            'consumerKey' => $marketplaceIntegration->meta['key'],
            'consumerSecret' => $marketplaceIntegration->meta['secret'],
        ]);

        $sdk->post('webhooks/batch', [
            'delete' => $marketplaceIntegration->meta['webhook_id'],
        ]);
    }

    public function validateHmac(Request $request)
    {
        $hmacHeader = $request->headers->get('x-wc-webhook-signature', '');
        $data = file_get_contents('php://input');

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, config('woocommerce.secret'), true));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function getTopicFromRequest(Request $request): string
    {
        return $request->headers->get('x-wc-webhook-topic');
    }

    public function mergeExtraDataBeforeProcess(Enumerable $rawData, Request $request): Enumerable
    {
        $woocommerceStoreUrl = $request->headers->get('x-wc-webhook-source');

        return $rawData->merge(['woocommerce_store_url' => $woocommerceStoreUrl]);
    }

    protected function createWebhook($topic)
    {
        return [
            'name' => 'GlaciusMSS',
            'topic' => $topic,
            'delivery_url' => config('woocommerce.webhook_url'),
            'secret' => config('woocommerce.secret'),
        ];
    }
}
