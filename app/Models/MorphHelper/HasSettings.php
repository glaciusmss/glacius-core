<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Models\MorphHelper;

use App\Models\Setting;

trait HasSettings
{
    /**
     * @return Setting
     */
    public function settings()
    {
        return $this->morphMany(Setting::class, 'settingable');
    }

    public function saveSetting($item, string $collection = 'general', string $type = 'update')
    {
        if ($type === 'update') {
            return $this->settings()
                ->whereSettingKey($item['setting_key'])
                ->whereCollection($collection)
                ->update(['setting_value' => $item['setting_value']]);
        }

        if (! $defaultType = $this->getSettingTypes($item['setting_key'])) {
            $defaultType = $item['type'];
        }

        return $this->settings()
            ->create([
                'setting_key' => $item['setting_key'],
                'collection' => $collection,
                'setting_value' => $item['setting_value'],
                'label' => $item['label'],
                'type' => $defaultType,
            ]);
    }

    /**
     * ex.
     * [
     *  'label' => 'Product Sync',
     *  'setting_key' => 'is_product_sync_activated',
     *  'setting_value' => true,
     *  'type' => 'boolean',
     * ].
     */
    public function createMultipleSettings(array $keyValuePairs, string $collection = 'general')
    {
        $result = [];

        foreach ($keyValuePairs as $item) {
            $result[] = $this->saveSetting($item, $collection, 'create');
        }

        return $result;
    }

    public function updateMultipleSettings(array $keyValuePairs, string $collection = 'general')
    {
        $result = [];

        foreach ($keyValuePairs as $item) {
            $result[] = $this->saveSetting($item, $collection, 'update');
        }

        return $result;
    }

    public function getSetting($key, $default = null, $collection = 'general')
    {
        $setting = $this->settings
            ->where('setting_key', $key)
            ->where('collection', $collection)
            ->first();

        if (! $setting) {
            return value($default);
        }

        return $setting->setting_value;
    }

    public function getMultipleSettings(array $keys, $defaults = null, $collection = 'general')
    {
        $result = [];

        foreach ($keys as $index => $item) {
            $result[$item] = $this->getSetting($item, $defaults[$index] ?? null, $collection);
        }

        return $result;
    }

    public function getAllSettings()
    {
        return $this->settings
            ->mapToGroups(function ($item) {
                return [
                    $item['collection'] => [
                        'label' => $item['label'],
                        'type' => $item['type'],
                        'setting_key' => $item['setting_key'],
                        'setting_value' => $item['setting_value'],
                    ],
                ];
            });
    }

    public function getAllSettingFromCollection(string $collection)
    {
        return $this->settings
            ->where('collection', $collection)
            ->toArray();
    }

    public function deleteSetting(string $key, string $collection = 'general')
    {
        return $this->settings()
            ->whereSettingKey($key)
            ->whereCollection($collection)
            ->delete();
    }

    public function deleteAllSettingsFromCollection(string $collection)
    {
        return $this->settings()
            ->whereCollection($collection)
            ->delete();
    }

    public function deleteAllSettings()
    {
        return $this->settings()
            ->delete();
    }

    public function deleteMultipleSettings(array $keys, $collection = 'general')
    {
        $result = [];

        foreach ($keys as $item) {
            $result[] = $this->deleteSetting($item, $collection);
        }

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
