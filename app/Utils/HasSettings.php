<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;


use App\Setting;

trait HasSettings
{
    /**
     * @return Setting
     */
    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    public function saveSetting($key, $value, $collection = 'general', $type = 'string')
    {
        if (!$defaultType = $this->getSettingTypes($key)) {
            $defaultType = $type;
        }

        return $this->settings()
            ->updateOrCreate(
                ['setting_key' => $key, 'collection' => $collection],
                ['setting_value' => $value, 'type' => $defaultType]
            );
    }

    /**
     * use default type(string)
     * ex. ['setting_key' => 'setting_value']
     *
     * use custom type
     * ex. ['setting_key' => ['setting_value' => 'type']]
     */
    public function saveMultipleSettings(array $keyValuePairs, $collection = 'general')
    {
        $result = [];

        collect($keyValuePairs)->each(function ($item, $index) use (&$result, $collection) {
            if (is_array($item)) {
                foreach ($item as $value => $type) {
                    $result[] = $this->saveSetting($index, $value, $collection, $type);
                }
            } else {
                $result[] = $this->saveSetting($index, $item, $collection);
            }
        });

        return $result;
    }

    public function getSetting($key, $default = null, $collection = 'general')
    {
        $setting = $this->settings()
            ->whereSettingKey($key)
            ->whereCollection($collection)
            ->first(['setting_value', 'type']);

        if (!$setting) {
            return value($default);
        }

        return $setting->setting_value;
    }

    public function getMultipleSettings(array $keys, $defaults = null, $collection = 'general')
    {
        $result = [];

        collect($keys)->each(function ($item, $index) use ($defaults, &$result) {
            $result[$item] = $this->getSetting($item, $defaults[$index] ?? null, $collection = 'general');
        });

        return $result;
    }

    public function getAllSettings()
    {
        return $this->settings()
            ->get(['setting_key', 'setting_value', 'type', 'collection'])
            ->mapWithKeys(function ($item) {
                return [
                    $item['collection'] => [
                        $item['setting_key'] => $item['setting_value']
                    ]
                ];
            });
    }

    public function getAllSettingFromCollection($collection)
    {
        return $this->settings()
            ->whereCollection($collection)
            ->get(['setting_key', 'setting_value', 'type'])
            ->mapWithKeys(function ($item) {
                return [$item['setting_key'] => $item['setting_value']];
            });
    }

    public function deleteSetting($key, $collection = 'general')
    {
        return $this->settings()
            ->whereSettingKey($key)
            ->whereCollection($collection)
            ->delete();
    }

    public function deleteAllSettingsFromCollection($collection)
    {
        return $this->settings()
            ->whereCollection($collection)
            ->delete();
    }

    public function deleteMultipleSettings(array $keys, $collection = 'general')
    {
        $result = [];

        collect($keys)->each(function ($item) use (&$result, $collection) {
            $result[] = $this->deleteSetting($item, $collection);
        });

        return $result;
    }

    protected function getSettingTypes($key)
    {
        if (isset($this->settingTypes[$key])) {
            return $this->settingTypes[$key];
        }

        return false;
    }
}
