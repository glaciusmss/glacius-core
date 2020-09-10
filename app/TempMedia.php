<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTempMedia
 */
class TempMedia extends Model
{
    protected $fillable = [
        'file_name', 'original_file_name', 'path'
    ];

    public function scopeExpired(Builder $query)
    {
        return $query->where('created_at', '<', now()->subMinutes(30));
    }
}
