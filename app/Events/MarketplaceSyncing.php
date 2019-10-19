<?php

namespace App\Events;

use App\Enums\EventType;
use App\Enums\SyncDirection;
use App\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarketplaceSyncing
{
    use Dispatchable, SerializesModels;

    public $event;
    public $model;
    public $direction;
    public $marketplace;
    public $syncState;

    public function __construct(EventType $event, Model $model, SyncDirection $direction, Marketplace $marketplace)
    {
        $this->event = $event;
        $this->model = $model;
        $this->direction = $direction;
        $this->marketplace = $marketplace;
    }
}
