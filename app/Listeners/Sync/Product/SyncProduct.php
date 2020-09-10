<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/9/2019
 * Time: 12:09 PM.
 */

namespace App\Listeners\Sync\Product;


use App\Enums\WebhookEventMapper;
use App\Enums\QueueGroup;
use App\Events\Product\ProductCreated;
use App\Events\Product\ProductDeleted;
use App\Events\Product\ProductUpdated;
use App\Services\SyncManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncProduct implements ShouldQueue
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
            return WebhookEventMapper::Created();
        }

        if ($event instanceof ProductUpdated) {
            return WebhookEventMapper::Updated();
        }

        if ($event instanceof ProductDeleted) {
            return WebhookEventMapper::Deleted();
        }

        return null;
    }
}
