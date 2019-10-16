<?php

namespace App\Events;

use App\DTO\SyncState;
use App\Enums\EventType;
use App\Enums\SyncDirection;
use App\Marketplace;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarketplaceSynced
{
    use Dispatchable, SerializesModels;

    public $event;
    public $model;
    public $direction;
    public $marketplace;
    public $syncState;

    public function __construct(EventType $event, Model $model, SyncDirection $direction, Marketplace $marketplace, SyncState $syncState)
    {
        $this->event = $event;
        $this->model = $model;
        $this->direction = $direction;
        $this->marketplace = $marketplace;
        $this->syncState = $syncState;
    }
}
