<?php

namespace App\Services\Woocommerce;

use App\Contracts\Connector;
use App\Models\Customer;
use App\Enums\WebhookEventMapper;
use App\Models\Order;
use App\Services\Woocommerce\Enums\WebhookTopic;
use App\Services\Woocommerce\Processors\CustomerProcessor;
use App\Services\Woocommerce\Processors\OrderProcessor;
use App\Services\Woocommerce\Syncs\SyncProduct;

class WoocommerceConnector implements Connector
{
    public function getConnectorIdentifier(): string
    {
        return 'woocommerce';
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
