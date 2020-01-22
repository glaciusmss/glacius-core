<?php

namespace App\Services;

use App\Contracts\Sync as SyncContract;
use App\MarketplaceIntegration;

abstract class BaseSync extends BaseMarketplace implements SyncContract
{
    protected $marketplaceIntegration;

    protected function log($message, $context = [])
    {
        \Log::channel('sync_' . $this->transformSyncForToModel())->info($message, $context ?? []);
    }

    protected function error($message, $context = [])
    {
        \Log::channel('sync_' . $this->transformSyncForToModel())->error($message, $context  ?? []);
    }

    public function setMarketplaceIntegration(MarketplaceIntegration $marketplaceIntegration)
    {
        $this->marketplaceIntegration = $marketplaceIntegration;
        return $this;
    }

    private function transformSyncForToModel()
    {
        return strtolower(class_basename($this->syncFor()));
    }

    abstract protected function syncFor();
}
