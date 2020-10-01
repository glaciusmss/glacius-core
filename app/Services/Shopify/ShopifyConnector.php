<?php

namespace App\Services\Shopify;

use App\Contracts\Connector;
use App\Enums\WebhookEventMapper;
use App\Models\Customer;
use App\Models\Order;
use App\Services\Shopify\Enums\WebhookTopic;
use App\Services\Shopify\Processors\CustomerProcessor;
use App\Services\Shopify\Processors\OrderProcessor;
use App\Services\Shopify\Syncs\SyncProduct;

class ShopifyConnector implements Connector
{
    public function getConnectorIdentifier(): string
    {
        return 'shopify';
    }

    public function getAuthService()
    {
        return OAuthService::class;
    }

    public function getWebhookService()
    {
        return WebhookService::class;
    }

    public function getSyncService(): array
    {
        return [
            SyncProduct::class,
        ];
    }

    public function getProcessorServices(): array
    {
        return [
            OrderProcessor::class,
            CustomerProcessor::class,
        ];
    }

    public function mapper(): array
    {
        return [
            'webhook' => [
                WebhookTopic::OrderCreate => [Order::class, WebhookEventMapper::Create],
                WebhookTopic::CustomerCreate => [Customer::class, WebhookEventMapper::Create],
                WebhookTopic::CustomerUpdate => [Customer::class, WebhookEventMapper::Update],
                WebhookTopic::CustomerDelete => [Customer::class, WebhookEventMapper::Delete],
            ],
        ];
    }
}
