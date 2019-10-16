<?php

namespace App\Listeners\Order;

use App\Enums\QueueGroup;
use App\Events\Order\OrderCreated;
use App\Notifications\Order\OrderCreated as OrderCreatedNotifcation;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderNotification implements ShouldQueue
{
    public $queue = QueueGroup::Notification;

    public function handle(OrderCreated $event)
    {
        $event->order->shop->notify(
            new OrderCreatedNotifcation($event->order)
        );
    }
}
