<?php

namespace App\Listeners\Sync;

use App\Enums\QueueGroup;
use App\Events\MarketplaceSynced;
use BotMan\BotMan\Interfaces\ShouldQueue;

class AddTransaction implements ShouldQueue
{
    public $queue = QueueGroup::Transaction;

    public function handle(MarketplaceSynced $marketplaceSynced)
    {
        if (!method_exists($marketplaceSynced->model, 'addSyncTransaction')) {
            return;
        }

        $marketplaceSynced->model->addSyncTransaction([
            'event' => $marketplaceSynced->event,
            'direction' => $marketplaceSynced->direction,
            'state' => $marketplaceSynced->syncState->getState(),
            'error_msg' => $marketplaceSynced->syncState->getErrorMsg(),
            'marketplace_id' => $marketplaceSynced->marketplace->id,
        ]);
    }
}
