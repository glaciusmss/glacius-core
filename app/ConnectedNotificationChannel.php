<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperConnectedNotificationChannel
 */
class ConnectedNotificationChannel extends Pivot
{
    protected $casts = [
        'meta' => 'array'
    ];
}
