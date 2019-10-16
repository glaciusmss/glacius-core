<?php

namespace App\Listeners\Sync;

use App\Events\MarketplaceSynced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddTransaction
{
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
