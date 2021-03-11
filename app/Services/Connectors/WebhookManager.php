<?php


namespace App\Services\Connectors;


use App\Contracts\Webhook;
use App\Events\Webhook\WebhookReceived;
use App\Models\MarketplaceIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

class WebhookManager
{
    protected $webhookService;
    protected $identifier;

    public function __construct(string $identifier, Webhook $service)
    {
        $this->webhookService = $service;
        $this->identifier = $identifier;
    }

    public function dispatchWebhookToProcessor(Request $request)
    {
        $topic = $this->getTopicFromRequest($request);
        $rawData = $this->mergeExtraDataBeforeProcess(Collection::wrap($request->all()), $request);

        WebhookReceived::dispatch($topic, $rawData, $this->identifier);
    }

    public function register(MarketplaceIntegration $marketplaceIntegration)
    {
        $this->webhookService->register($marketplaceIntegration);
    }

    public function remove(MarketplaceIntegration $marketplaceIntegration)
    {
        $this->webhookService->remove($marketplaceIntegration);
    }

    public function validateHmac(Request $request)
    {
        return $this->webhookService->validateHmac($request);
    }

    public function getTopicFromRequest(Request $request)
    {
        return $this->webhookService->getTopicFromRequest($request);
    }

    public function mergeExtraDataBeforeProcess(Enumerable $rawData, Request $request)
    {
        return $this->webhookService->mergeExtraDataBeforeProcess($rawData, $request);
    }
}
