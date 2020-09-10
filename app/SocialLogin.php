<?php

namespace App;

use App\Enums\SocialProvider;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSocialLogin
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
