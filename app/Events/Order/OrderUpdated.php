<?php

namespace App\Events\Order;

use Illuminate\Broadcasting\InteractsWithSockets;

class OrderUpdated extends BaseOrderEvent
{
    use InteractsWithSockets;
}
