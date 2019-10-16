<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/15/2019
 * Time: 10:19 AM.
 */

namespace App\Listeners\Webhook;


use App\Contracts\Processor;
use App\Enums\MarketplaceEnum;
use App\Enums\ProcessorType;
use App\Enums\QueueGroup;
use App\Events\Webhook\OrderCreateReceivedFromMarketplace;
use App\Services\ProcessorFactory;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOrderFromMarketplace implements ShouldQueue
{
    public $queue = QueueGroup::Webhook;
    protected $processorFactory;

    public function __construct(ProcessorFactory $processorFactory)
    {
        $this->processorFactory = $processorFactory;
    }

    public function handle(OrderCreateReceivedFromMarketplace $orderCreateReceivedFromMarketplace)
    {
        $processor = $this->processorFactory
            ->setProcessorType(ProcessorType::Order())
            ->setMarketplace($orderCreateReceivedFromMarketplace->marketplace)
            ->build();

        if (!$processor) {
            return;
        }

        $processor->process($orderCreateReceivedFromMarketplace);
    }
}
