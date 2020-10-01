<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/21/2019
 * Time: 5:43 PM.
 */

namespace App\Contracts;

use App\DTO\SyncState;
use App\Events\SyncEvent;
use App\Models\MarketplaceIntegration;

interface Sync
{
    public function onCreate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState;

    public function onUpdate(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState;

    public function onDelete(SyncEvent $event, MarketplaceIntegration $marketplaceIntegration): SyncState;

    public function processFor(): string;

    public function withExisting();
}
