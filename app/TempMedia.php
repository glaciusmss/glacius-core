<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\TempMedia
 *
 * @property int $id
 * @property string $file_name
 * @property string $original_file_name
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia whereOriginalFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TempMedia expired()
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
