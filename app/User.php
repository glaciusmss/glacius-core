<?php

namespace App;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Yadahan\AuthenticationLog\AuthenticationLogable;

/**
 * App\User.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \App\Utils\CarbonFix|null $email_verified_at
 * @property string $password
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Yadahan\AuthenticationLog\AuthenticationLog[] $authentications
 * @property-read int|null $authentications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NotificationChannel[] $notificationChannels
 * @property-read int|null $notification_channels_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Shop[] $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SocialLogin[] $socialLogins
 * @property-read int|null $social_logins_count
 * @property-read \App\UserProfile|null $userProfile
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, AuthenticationLogable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'user_shops')
            ->withTimestamps()
            ->using(UserShop::class);
    }

    public function notificationChannels()
    {
        return $this->belongsToMany(NotificationChannel::class, 'connected_notification_channels')
            ->withTimestamps()
            ->withPivot(['meta'])
            ->using(ConnectedNotificationChannel::class);
    }

    public function socialLogins()
    {
        return $this->hasMany(SocialLogin::class);
    }

    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
