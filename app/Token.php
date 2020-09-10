<?php

namespace App;

use App\Enums\TokenType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperToken
 */
class Token extends Model
{
    protected $fillable = [
        'token', 'type', 'meta', 'expired_at',
    ];

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

    public static function validate($token, TokenType $type)
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

    public static function validateAndDelete($token, TokenType $type)
    {
        if (!$instance = self::validate($token, $type)) {
            return false;
        }

        $clonedInstance = $instance->replicate();

        $instance->delete();

        return $clonedInstance;
    }
}
