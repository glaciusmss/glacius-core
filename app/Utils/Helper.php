<?php


namespace App\Utils;


use Illuminate\Support\Collection;

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

    public static function transformArrayKey($array, $keysToTransform): array
    {
        return Collection::wrap($array)
            ->map(function ($item, $key) use ($keysToTransform) {
                if (array_key_exists($key, $keysToTransform)) {
                    return [$keysToTransform[$key] => $item];
                }

                return [$key => $item];
            })
            ->collapse()
            ->toArray();
    }

    public static function escapeElasticReservedChars($string)
    {
        $regex = "/[\\+\\-\\=\\&\\|\\!\\(\\)\\{\\}\\[\\]\\^\\\"\\~\\*\\<\\>\\?\\:\\\\\\/]/";

        return preg_replace($regex, addslashes('\\$0'), $string);
    }
}
