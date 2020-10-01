<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\TempMedia.
 *
 * @property int $id
 * @property string $file_name
 * @property string $original_file_name
 * @property string $path
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @method static Builder|TempMedia expired()
 * @method static Builder|TempMedia newModelQuery()
 * @method static Builder|TempMedia newQuery()
 * @method static Builder|TempMedia query()
 * @method static Builder|TempMedia whereCreatedAt($value)
 * @method static Builder|TempMedia whereFileName($value)
 * @method static Builder|TempMedia whereId($value)
 * @method static Builder|TempMedia whereOriginalFileName($value)
 * @method static Builder|TempMedia wherePath($value)
 * @method static Builder|TempMedia whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TempMedia extends Model
{
    protected $guarded = [];

    public function scopeExpired(Builder $query)
    {
        return $query->where('created_at', '<', now()->subDay());
    }
}
