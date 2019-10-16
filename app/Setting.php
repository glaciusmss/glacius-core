<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Setting
 *
 * @property int $id
 * @property string $setting_key
 * @property string|null $setting_value
 * @property string $type
 * @property string $collection
 * @property string $settingable_type
 * @property int $settingable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $settingable
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereSettingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereSettingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereSettingableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereSettingableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Setting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'type', 'collection'];

    protected $casts = ['setting_value' => 'string'];

    protected function getCastType($key)
    {
        if ($key === 'setting_value' && $this->type) {
            return $this->type;
        }

        return parent::getCastType($key);
    }

    public function settingable()
    {
        return $this->morphTo();
    }
}
