<?php

namespace App\Events\Product;

use App\Enums\SyncEventMapper;
use App\Events\SyncEvent;

class ProductUpdated extends SyncEvent
{
    public function getMethod()
    {
        return SyncEventMapper::Update;
    }
}
