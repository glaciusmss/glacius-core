<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\NotificationChannel.
 *
 * @property int $id
 * @property string $name
 * @property string $website
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationChannel whereWebsite($value)
 * @mixin \Eloquent
 */
class NotificationChannel extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'connected_notification_channels')
            ->withTimestamps()
            ->withPivot(['meta'])
            ->using(ConnectedNotificationChannel::class);
    }
}
