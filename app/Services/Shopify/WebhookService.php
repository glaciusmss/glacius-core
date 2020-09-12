<?php


namespace App\Services\Shopify;


use App\Contracts\Webhook;
use App\MarketplaceIntegration;
use App\Services\Shopify\Enums\WebhookTopic;
use App\Services\Shopify\Helpers\HasSdk;
use Illuminate\Http\Request;
use Illuminate\Support\Enumerable;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

class WebhookService implements Webhook
{
    use HasSdk;

    public function register(MarketplaceIntegration $marketplaceIntegration)
    {
        $webhookIds = [];
        $topics = WebhookTopic::getValues();
        $sdk = $this->getSdk([
            'ShopUrl' => $marketplaceIntegration->meta['shopify_shop'],
            'AccessToken' => $marketplaceIntegration->token,
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
        // simply leave this empty, shopify will clear this when oauth deleted
    }

    public function validateHmac(Request $request)
    {
        $hmacHeader = $request->headers->get('x-shopify-hmac-sha256', '');
        $data = file_get_contents('php://input');

        $calculatedHmac = base64_encode(hash_hmac('sha256', $data, config('marketplace.shopify.secret'), true));

        return hash_equals($hmacHeader, $calculatedHmac);
    }

    public function getTopicFromRequest(Request $request): string
    {
        return $request->headers->get('x-shopify-topic');
    }

    public function mergeExtraDataBeforeProcess(Enumerable $rawData, Request $request): Enumerable
    {
        $shopDomain = $request->headers->get('x-shopify-shop-domain');

        return $rawData->merge(['shop_domain' => $shopDomain]);
    }

    protected function createWebhook($topic, ShopifySDK $sdk)
    {
        try {
            $response = $sdk->Webhook
                ->post([
                    'topic' => $topic,
                    'address' => config('marketplace.shopify.webhook_url'),
                    'format' => 'json',
                ]);

            return $response['id'];
        } catch (ApiException $exception) {
            //ignore this if already created
            return null;
        }
    }
}
