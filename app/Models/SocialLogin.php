<?php

namespace App\Models;

use App\Enums\SocialProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SocialLogin
 *
 * @property int $id
 * @property string $provider_user_id
 * @property SocialProvider $provider
 * @property int $user_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereProviderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialLogin whereUserId($value)
 * @mixin \Eloquent
 */
class SocialLogin extends Model
{
    protected $guarded = [];

    public $casts = [
        'provider' => SocialProvider::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
