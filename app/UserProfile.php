<?php

namespace App;

use App\Enums\GenderEnum;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

/**
 * App\UserProfile
 *
 * @property int $id
 * @property string|null $phone_number
 * @property int|null $gender
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\UserProfile whereUserId($value)
 * @mixin \Eloquent
 */
class UserProfile extends Model
{
    use CastsEnums;

    protected $fillable = [
        'phone_number', 'gender', 'date_of_birth', 'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
    ];

    protected $enumCasts = [
        'gender' => GenderEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
