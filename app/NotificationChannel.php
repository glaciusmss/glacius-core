<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\NotificationChannel
 *
 * @property int $id
 * @property string $name
 * @property string $website
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationChannel whereWebsite($value)
 * @mixin \Eloquent
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
