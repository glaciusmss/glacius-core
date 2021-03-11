<?php

namespace App\Listeners\Webhook;

use App\Enums\QueueGroup;
use App\Enums\ServiceMethod;
use App\Events\Webhook\WebhookReceived;
use App\Services\Connectors\ManagerBuilder;
use App\Services\Connectors\ProcessorManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessWebhook implements ShouldQueue
{
    public $queue = QueueGroup::Webhook;
    protected $managerBuilder;

    public function __construct(ManagerBuilder $managerBuilder)
    {
        $this->managerBuilder = $managerBuilder;
    }

    public function handle(WebhookReceived $webhookReceived)
    {
        /** @var ProcessorManager $processorManager */
        $processorManager = $this->managerBuilder
            ->setIdentifier($webhookReceived->identifier)
            ->setManagerClass(ProcessorManager::class)
            ->setServiceMethod(ServiceMethod::ProcessorService)
            ->build();

        $processorManager
            ->setWebhookReceivedEvent($webhookReceived)
            ->process();
    }
}
