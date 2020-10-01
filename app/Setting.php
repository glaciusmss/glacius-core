<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Setting.
 *
 * @property int $id
 * @property string $label
 * @property string $setting_key
 * @property string|null $setting_value
 * @property string $type
 * @property string $collection
 * @property string $settingable_type
 * @property int $settingable_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read Model|\Eloquent $settingable
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSettingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSettingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSettingableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSettingableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $guarded = [];

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
