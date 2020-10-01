<?php

namespace App\Events;

use App\Enums\SyncDirection;
use App\Enums\WebhookEventMapper;
use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class MarketplaceSyncing
{
    use Dispatchable;

    public $event;
    public $model;
    public $direction;
    public $marketplace;
    public $syncState;

    public function __construct(WebhookEventMapper $event, Model $model, SyncDirection $direction, Marketplace $marketplace)
    {
        $this->event = $event;
        $this->model = $model;
        $this->direction = $direction;
        $this->marketplace = $marketplace;
    }
}
