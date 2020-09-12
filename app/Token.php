<?php

namespace App;

use App\Enums\TokenType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Token
 *
 * @property int $id
 * @property string $token
 * @property TokenType $type
 * @property array $meta
 * @property \App\Utils\CarbonFix $expired_at
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @method static Builder|Token expired()
 * @method static Builder|Token isType(\App\Enums\TokenType $type)
 * @method static Builder|Token isValid()
 * @method static Builder|Token newModelQuery()
 * @method static Builder|Token newQuery()
 * @method static Builder|Token query()
 * @method static Builder|Token whereCreatedAt($value)
 * @method static Builder|Token whereExpiredAt($value)
 * @method static Builder|Token whereId($value)
 * @method static Builder|Token whereMeta($value)
 * @method static Builder|Token whereToken($value)
 * @method static Builder|Token whereType($value)
 * @method static Builder|Token whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Token extends Model
{
    protected $guarded = [];

    protected $dates = [
        'expired_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'type' => TokenType::class,
    ];

    public function scopeExpired(Builder $query)
    {
        return $query->where('expired_at', '<', now());
    }

    public function scopeIsValid(Builder $query)
    {
        return $query->where('expired_at', '>=', now());
    }

    public function scopeIsType(Builder $query, TokenType $type)
    {
        return $query->where('type', $type->value);
    }

    public static function generate(TokenType $type, array $meta = [], int $duration = 15, int $length = 16)
    {
        $expiredAt = now()->addMinutes($duration);

        $token = md5(Str::random($length) . microtime());

        return self::make([
            'token' => $token,
            'type' => $type,
            'meta' => $meta,
            'expired_at' => $expiredAt
        ]);
    }

    public static function generateAndSave(TokenType $type, array $meta = [], int $duration = 15, int $length = 16)
    {
        $instance = self::generate($type, $meta, $duration, $length);
        $instance->save();

        return $instance;
    }

    public static function validate(string $token, TokenType $type)
    {
        $instance = self::whereToken($token)
            ->isType($type)
            ->isValid()
            ->first();

        if (!$instance) {
            return false;
        }

        return $instance;
    }

    public static function validateAndDelete(string $token, TokenType $type)
    {
        if (!$instance = self::validate($token, $type)) {
            return false;
        }

        $clonedInstance = $instance->replicate();

        $instance->delete();

        return $clonedInstance;
    }
}
