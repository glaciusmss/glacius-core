<?php

namespace App;

use App\Enums\EventType;
use App\Enums\State;
use App\Enums\SyncDirection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\SyncTransaction
 *
 * @property int $id
 * @property \App\Enums\EventType|int $event
 * @property \App\Enums\SyncDirection|int $direction
 * @property string $sync_transactional_type
 * @property int $sync_transactional_id
 * @property int $marketplace_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Marketplace $marketplace
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $syncTransactional
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereSyncTransactionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereSyncTransactionalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property \App\Enums\State|int $state
 * @property string|null $error_msg
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereErrorMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SyncTransaction whereState($value)
 */
class SyncTransaction extends Model
{
    protected $fillable = [
        'event', 'direction', 'state', 'error_msg', 'marketplace_id'
    ];

    protected $casts = [
        'event' => EventType::class,
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
