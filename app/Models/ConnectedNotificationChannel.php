<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\ConnectedNotificationChannel
 *
 * @property int $id
 * @property array $meta
 * @property int $user_id
 * @property int $notification_channel_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel whereNotificationChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ConnectedNotificationChannel whereUserId($value)
 * @mixin \Eloquent
 */
class ConnectedNotificationChannel extends Pivot
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'connected_notification_channels';

    protected $casts = [
        'meta' => 'array',
    ];
}
