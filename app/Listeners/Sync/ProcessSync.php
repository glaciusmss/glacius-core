<?php

namespace App\Listeners\Sync;

use App\Enums\QueueGroup;
use App\Enums\ServiceMethod;
use App\Events\SyncEvent;
use App\Services\Connectors\ManagerBuilder;
use App\Services\Connectors\SyncManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessSync implements ShouldQueue
{
    public $queue = QueueGroup::Sync;
    protected $managerBuilder;

    public function __construct(ManagerBuilder $managerBuilder)
    {
        $this->managerBuilder = $managerBuilder;
    }

    /**
     * @param SyncEvent $syncEvent
     */
    public function handle($syncEvent)
    {
        foreach ($syncEvent->shop->marketplaces as $marketplace) {
            /** @var SyncManager $syncManager */
            $syncManager = $this->managerBuilder
                ->setIdentifier($marketplace->name)
                ->setManagerClass(SyncManager::class)
                ->setServiceMethod(ServiceMethod::SyncService)
                ->build();

            $syncManager->setMarketplace($marketplace)
                ->setSyncEvent($syncEvent)
                ->sync();
        }
    }
}
