<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperSetting
 */
class Setting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'label', 'type', 'collection'];

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
