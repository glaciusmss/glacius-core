<?php

namespace App\Events\Product;

use App\Enums\SyncEventMapper;
use App\Events\SyncEvent;

class ProductCreated extends SyncEvent
{
    public function getMethod()
    {
        return SyncEventMapper::Create;
    }
}
