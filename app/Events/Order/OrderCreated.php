<?php

namespace App\Events\Order;

use Illuminate\Broadcasting\InteractsWithSockets;

class OrderCreated extends BaseOrderEvent
{
    use InteractsWithSockets;
}
