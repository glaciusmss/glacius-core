<?php

namespace App;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperUserProfile
 */
class UserProfile extends Model
{
    protected $fillable = [
        'phone_number', 'gender', 'date_of_birth', 'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
        'gender' => GenderEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
