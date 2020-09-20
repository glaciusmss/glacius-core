<?php


namespace App\Services\Easystore;


use App\Contracts\Webhook;
use App\MarketplaceIntegration;
use App\Services\Easystore\Enums\WebhookTopic;
use App\Services\Easystore\Helpers\HasSdk;
use EasyStore\Client as EasyStoreClient;
use EasyStore\Exception\ApiException;
use Illuminate\Http\Request;
use Illuminate\Support\Enumerable;

class WebhookService implements Webhook
{
    use HasSdk;

    public function register(MarketplaceIntegration $marketplaceIntegration)
    {
        $webhookIds = [];
        $topics = WebhookTopic::getValues();
        $sdk = $this->getSdk([
            'shop' => $marketplaceIntegration->meta['easystore_shop'],
            'access_token' => $marketplaceIntegration->token,
        ]);

        foreach ($topics as $topic) {
            $webhookId = $this->createWebhook($topic, $sdk);
            if ($webhookId !== null) {
                $webhookIds[] = $webhookId;
            }
        }

        $marketplaceIntegration->update([
            'meta->webhook_id' => $webhookIds
        ]);
    }

    public function remove(MarketplaceIntegration $marketplaceIntegration)
    {
        $sdk = $this->getSdk([
            'access_token' => $marketplaceIntegration->token,
            'shop' => $marketplaceIntegration->meta['easystore_shop'],
        ]);

        foreach ($marketplaceIntegration->meta['webhook_id'] as $webhookId) {
            $sdk->delete("/webhooks/{$webhookId}.json");
        }
    }

    public function validateHmac(Request $request)
    {
        $hmacHeader = $request->headers->get('easystore-hmac-sha256', '');
        $data = file_get_contents('php://input');

        $calculatedHmac = hash_hmac('sha256', $data, config('easystore.secret'));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function getTopicFromRequest(Request $request): string
    {
        return $request->headers->get('easystore-topic');
    }

    public function mergeExtraDataBeforeProcess(Enumerable $rawData, Request $request): Enumerable
    {
        $shopDomain = $request->headers->get('easystore-shop-domain');

        return $rawData->merge(['shop_domain' => $shopDomain]);
    }

    protected function createWebhook($topic, EasyStoreClient $sdk)
    {
        try {
            $response = $sdk->post('/webhooks.json', [
                'webhook' => [
                    'topic' => $topic,
                    'url' => config('easystore.webhook_url'),
                ]
            ]);

            return $response['id'];
        } catch (ApiException $exception) {
            //ignore this if already created
            return null;
        }
    }
}
