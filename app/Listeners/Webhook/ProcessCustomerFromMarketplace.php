<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/15/2019
 * Time: 10:19 AM.
 */

namespace App\Listeners\Webhook;


use App\Enums\ProcessorType;
use App\Enums\QueueGroup;
use App\Services\ProcessorFactory;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessCustomerFromMarketplace implements ShouldQueue
{
    public $queue = QueueGroup::Webhook;
    protected $processorFactory;

    public function __construct(ProcessorFactory $processorFactory)
    {
        $this->processorFactory = $processorFactory;
    }

    public function handle($customerWebhookReceivedFromMarketplace)
    {
        $processor = $this->processorFactory
            ->setProcessorType(ProcessorType::Customer())
            ->setMarketplace($customerWebhookReceivedFromMarketplace->marketplace)
            ->build();

        if (!$processor) {
            return;
        }

        $processor->process($customerWebhookReceivedFromMarketplace);
    }
}
