<?php

namespace App;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Yadahan\AuthenticationLog\AuthenticationLogable;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, AuthenticationLogable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
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
