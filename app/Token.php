<?php

namespace App;

use App\Enums\TokenType;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Token
 *
 * @property \App\Enums\TokenType|null $type
 * @property int $id
 * @property string $token
 * @property array $meta
 * @property \Illuminate\Support\Carbon $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Token whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Token extends Model
{
    use CastsEnums;

    protected $fillable = [
        'token', 'type', 'meta', 'expired_at',
    ];

    protected $dates = [
        'expired_at'
    ];

    protected $enumCasts = [
        'type' => TokenType::class
    ];

    protected $casts = [
        'meta' => 'array',
        'type' => 'int',
    ];

    public function scopeExpired(Builder $query)
    {
        return $query->where('expired_at', '<', now());
    }

    public function scopeIsValid(Builder $query)
    {
        return $query->where('expired_at', '>=', now());
    }

    public static function generate(TokenType $type, $meta = [], $duration = 15, $length = 16)
    {
        if (is_int($duration)) {
            $expiredAt = now()->addMinutes($duration);
        }

        $token = md5(Str::random($length) . microtime());

        return self::make([
            'token' => $token,
            'type' => $type,
            'meta' => $meta,
            'expired_at' => $expiredAt
        ]);
    }

    public static function generateAndSave(TokenType $type, $meta = [], $duration = 15, $length = 16)
    {
        $instance = self::generate($type, $meta, $duration, $length);
        $instance->save();

        return $instance;
    }

    public static function validate($token)
    {
        $instance = self::whereToken($token)
            ->isValid()
            ->first();

        if (!$instance) {
            return false;
        }

        return $instance;
    }

    public static function validateAndDelete($token)
    {
        if (!$instance = self::validate($token)) {
            return false;
        }

        $clonedInstance = $instance->replicate();

        $instance->delete();

        return $clonedInstance;
    }
}
