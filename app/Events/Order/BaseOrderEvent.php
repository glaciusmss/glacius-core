<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/22/2019
 * Time: 4:50 PM.
 */

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BaseOrderEvent
{
    use Dispatchable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
