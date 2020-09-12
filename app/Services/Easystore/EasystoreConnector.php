<?php


namespace App\Services\Easystore;


use App\Contracts\Connector;
use App\Customer;
use App\Enums\WebhookEventMapper;
use App\Order;
use App\Services\Easystore\Enums\WebhookTopic;
use App\Services\Easystore\Processors\CustomerProcessor;
use App\Services\Easystore\Processors\OrderProcessor;
use App\Services\Easystore\Syncs\SyncProduct;

class EasystoreConnector implements Connector
{
    public function getConnectorIdentifier(): string
    {
        return 'easystore';
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
            SyncProduct::class
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
                WebhookTopic::CustomerDelete => [Customer::class, WebhookEventMapper::Delete],
            ]
        ];
    }
}
