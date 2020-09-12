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

    public function saveSetting($item, string $collection = 'general')
    {
        if (!$defaultType = $this->getSettingTypes($item['setting_key'])) {
            $defaultType = $item['type'];
        }

        return $this->settings()
            ->updateOrCreate(
                ['setting_key' => $item['setting_key'], 'collection' => $collection],
                ['setting_value' => $item['setting_value'], 'label' => $item['label'], 'type' => $defaultType]
            );
    }

    /**
     * ex.
     * [
     *  'label' => 'Product Sync',
     *  'setting_key' => 'is_product_sync_activated',
     *  'setting_value' => true,
     *  'type' => 'boolean',
     * ]
     */
    public function saveMultipleSettings(array $keyValuePairs, string $collection = 'general')
    {
        $result = [];

        foreach ($keyValuePairs as $item) {
            $result[] = $this->saveSetting($item, $collection);
        }

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

        foreach ($keys as $index => $item) {
            $result[$item] = $this->getSetting($item, $defaults[$index] ?? null, $collection);
        }

        return $result;
    }

    public function getAllSettings()
    {
        return $this->settings()
            ->get(['setting_key', 'setting_value', 'label', 'type', 'collection'])
            ->mapWithKeys(function ($item) {
                return [
                    $item['collection'] => [
                        'label' => $item['label'],
                        'type' => $item['type'],
                        'setting_key' => $item['setting_key'],
                        'setting_value' => $item['setting_value'],
                    ]
                ];
            });
    }

    public function getAllSettingFromCollection(string $collection)
    {
        return $this->settings()
            ->whereCollection($collection)
            ->get(['setting_key', 'setting_value', 'label', 'type'])
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
