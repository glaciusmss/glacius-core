<?php


namespace App\Listeners\Sync;


use App\Contracts\Sync;
use App\Enums\QueueGroup;
use App\Enums\SyncDirection;
use App\Events\MarketplaceSynced;
use App\Events\SyncEvent;
use App\Marketplace;
use App\MarketplaceIntegration;
use App\Services\Connectors\ConnectorManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class ProcessSync implements ShouldQueue
{
    public $queue = QueueGroup::Sync;
    protected $connectorManager;

    public function __construct(ConnectorManager $connectorManager)
    {
        $this->connectorManager = $connectorManager;
    }

    /**
     * @param SyncEvent $syncEvent
     */
    public function handle($syncEvent)
    {
        $modelClassName = get_class($syncEvent->model);
        foreach ($syncEvent->shop->marketplaces as $marketplace) {
            /** @var MarketplaceIntegration $marketplaceIntegration */
            $marketplaceIntegration = $marketplace->pivot;

            $connector = $this->connectorManager->resolveConnector($marketplace->name);

            // dispatch sync
            $syncServices = Collection::wrap($connector->getSyncService());

            // identify which processor to be used
            $resolvedSync = $syncServices->map(function ($syncService) {
                return $this->connectorManager->makeService($syncService);
            })->first(static function (Sync $syncService) use ($modelClassName) {
                return $syncService->processFor() === $modelClassName;
            });

            if (!$resolvedSync || !$this->isSyncEnabled($marketplace, $syncEvent)) {
                // sync service not activated for current marketplace
                // or is disabled
                continue;
            }

            // call the specific sync service
            $result = $resolvedSync->{$syncEvent->getMethod()}($syncEvent, $marketplaceIntegration);

            //fire synced event after we finished each sync
            event(new MarketplaceSynced($syncEvent->getMethod(), $syncEvent->model, SyncDirection::To(), $marketplace, $result));
        }
    }

    protected function isSyncEnabled(Marketplace $marketplace, SyncEvent $syncEvent)
    {
        $optionKey = 'is_' . strtolower(class_basename($syncEvent->model)) . '_sync_activated';

        return $syncEvent->shop->getSetting($optionKey, true, $marketplace->name);
    }
}
