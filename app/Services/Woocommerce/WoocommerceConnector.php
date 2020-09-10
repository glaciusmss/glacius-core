<?php


namespace App\Services\Woocommerce;


use App\Contracts\Connector;
use App\Customer;
use App\Enums\WebhookEventMapper;
use App\Enums\Woocommerce\WebhookTopic;
use App\Order;
use App\Services\Woocommerce\Processors\CustomerProcessor;
use App\Services\Woocommerce\Processors\OrderProcessor;

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
        // TODO: Implement getSyncService() method.
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
            ]
        ];
    }
}