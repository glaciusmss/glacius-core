<?php


namespace App\Services\Connectors;


use App\Contracts\Sync;
use App\Enums\SyncDirection;
use App\Events\MarketplaceSynced;
use App\Events\SyncEvent;
use App\Models\Marketplace;
use Illuminate\Support\Enumerable;

class SyncManager
{
    protected $identifier;
    protected $syncServices;
    protected $syncEvent;
    protected $marketplace;

    public function __construct(string $identifier, Enumerable $service)
    {
        $this->identifier = $identifier;
        $this->syncServices = $service;
    }

    public function sync()
    {
        $resolvedSync = $this->resolvedSync();

        if (!$resolvedSync || !$this->isSyncEnabled()) {
            // sync service not activated for current marketplace
            // or is disabled
            return;
        }

        // call the specific sync service
        $result = $resolvedSync->{$this->syncEvent->getMethod()}($this->syncEvent, $this->marketplace->pivot);

        // fire synced event after we finished sync
        event(
            new MarketplaceSynced(
                $this->syncEvent->getMethod(),
                $this->syncEvent->model,
                SyncDirection::To(),
                $this->marketplace,
                $result
            )
        );
    }

    protected function resolvedSync()
    {
        return $this->syncServices
            ->first(function (Sync $syncService) {
                return $syncService->processFor() === get_class($this->syncEvent->model);
            });
    }

    public function setSyncEvent(SyncEvent $syncEvent): SyncManager
    {
        $this->syncEvent = $syncEvent;
        return $this;
    }

    public function setMarketplace(Marketplace $marketplace): SyncManager
    {
        $this->marketplace = $marketplace;
        return $this;
    }

    protected function isSyncEnabled()
    {
        $optionKey = 'is_' . strtolower(class_basename($this->syncEvent->model)) . '_sync_activated';

        return $this->syncEvent->shop->getSetting($optionKey, true, $this->marketplace->name);
    }
}
