<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/9/2019
 * Time: 12:09 PM.
 */

namespace App\Listeners\Sync\Product;


use App\Enums\EventType;
use App\Enums\QueueGroup;
use App\Events\Product\ProductCreated;
use App\Events\Product\ProductDeleted;
use App\Events\Product\ProductUpdated;
use App\Services\SyncManager;

class SyncProduct
{
    public $queue = QueueGroup::Sync;

    public function handle($event)
    {
        if (!$eventType = $this->type($event)) {
            return;
        }

        $syncManager = new SyncManager($event->product->shop, $event->product);
        $syncManager->syncWith($eventType);
    }

    protected function type($event)
    {
        if ($event instanceof ProductCreated) {
            return EventType::Created();
        }

        if ($event instanceof ProductUpdated) {
            return EventType::Updated();
        }

        if ($event instanceof ProductDeleted) {
            return EventType::Deleted();
        }

        return null;
    }
}
