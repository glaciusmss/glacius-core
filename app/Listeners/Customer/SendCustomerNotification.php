<?php

namespace App\Listeners\Customer;

use App\Enums\QueueGroup;
use App\Events\Customer\CustomerCreated;
use App\Notifications\Customer\CustomerCreated as CustomerCreatedNotifcation;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomerNotification implements ShouldQueue
{
    public $queue = QueueGroup::Notification;

    public function handle(CustomerCreated $event)
    {
        $event->customer->shop->notify(
            new CustomerCreatedNotifcation($event->customer)
        );
    }
}
