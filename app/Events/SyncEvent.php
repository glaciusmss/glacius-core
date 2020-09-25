<?php

namespace App\Events;

use App\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class SyncEvent
{
    use Dispatchable, SerializesModels;

    public $model;
    public $shop;

    public function __construct(Model $model, Shop $shop)
    {
        $this->model = $model;
        $this->shop = $shop;
    }

    abstract public function getMethod();
}
