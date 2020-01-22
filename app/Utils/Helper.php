<?php


namespace App\Utils;


class Helper
{
    public static function calculatePercentage($oldValue, $newValue)
    {
        if ($oldValue === 0 && $newValue === 0) {
            return 0;
        }

        if ($oldValue === 0 && $newValue > 0) {
            return 100;
        }

        if ($oldValue > 0 && $newValue === 0) {
            return -100;
        }

        return (1 - $oldValue / $newValue) * 100;
    }

    public static function transformArrayKey($array, $keysToTransform)
    {
        $temp = [];
        foreach ($array as $key => $value) {
            $transformed = false;

            foreach ($keysToTransform as $oriKey => $expectedKey) {
                if ($oriKey === $key) {
                    $temp[$expectedKey] = $value;
                    $transformed = true;
                    break;
                }
            }

            if (!$transformed) {
                $temp[$key] = $value;
            }
        }

        return $temp;
    }
}
