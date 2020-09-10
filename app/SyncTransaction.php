<?php

namespace App;

use App\Enums\WebhookEventMapper;
use App\Enums\State;
use App\Enums\SyncDirection;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSyncTransaction
 */
class SyncTransaction extends Model
{
    protected $fillable = [
        'event', 'direction', 'state', 'error_msg', 'marketplace_id'
    ];

    protected $casts = [
        'event' => WebhookEventMapper::class,
        'direction' => SyncDirection::class,
        'state' => State::class,
    ];

    public function syncTransactional()
    {
        return $this->morphTo();
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
