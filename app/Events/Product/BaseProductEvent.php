<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/22/2019
 * Time: 4:49 PM.
 */

namespace App\Events\Product;


use App\Product;
use Illuminate\Foundation\Events\Dispatchable;

class BaseProductEvent
{
    use Dispatchable;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
