<?php

namespace App\Models;

use App\Enums\State;
use App\Enums\SyncDirection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\SyncTransaction.
 *
 * @property int $id
 * @property string $event
 * @property SyncDirection $direction
 * @property State $state
 * @property string|null $error_msg
 * @property string $sync_transactional_type
 * @property int $sync_transactional_id
 * @property int $marketplace_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \App\Marketplace $marketplace
 * @property-read Model|\Eloquent $syncTransactional
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereErrorMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereSyncTransactionalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereSyncTransactionalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SyncTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SyncTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
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
