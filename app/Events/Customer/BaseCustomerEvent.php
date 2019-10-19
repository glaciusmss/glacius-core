<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/22/2019
 * Time: 4:50 PM.
 */

namespace App\Events\Customer;


use App\Customer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BaseCustomerEvent
{
    use Dispatchable, SerializesModels;

    public $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
