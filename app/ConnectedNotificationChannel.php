<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\ConnectedNotificationChannel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConnectedNotificationChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConnectedNotificationChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ConnectedNotificationChannel query()
 * @mixin \Eloquent
 */
class ConnectedNotificationChannel extends Pivot
{
    protected $casts = [
        'meta' => 'array'
    ];
}
