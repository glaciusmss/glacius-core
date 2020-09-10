<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperNotificationChannel
 */
class NotificationChannel extends Model
{
    protected $fillable = ['name', 'website'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'connected_notification_channels')
            ->withTimestamps()
            ->withPivot(['meta'])
            ->using(ConnectedNotificationChannel::class);
    }
}
