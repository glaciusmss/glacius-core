<?php

namespace App;

use App\Enums\SocialProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * App\SocialLogin
 *
 * @property int $id
 * @property string $provider_user_id
 * @property string $provider
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin whereProviderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialLogin whereUserId($value)
 * @mixin \Eloquent
 */
class SocialLogin extends Model
{
    protected $fillable = [
        'provider_user_id', 'provider'
    ];

    public $casts = [
        'provider' => SocialProvider::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
