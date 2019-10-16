<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/29/2019
 * Time: 10:02 AM.
 */
declare(strict_types=1);

namespace App\Utils;

use Illuminate\Support\Carbon;
use function in_array;
use function is_string;
use const null;
use const true;

class CarbonFix extends Carbon
{
    /**
     * @param array|string $name
     * @param mixed $value
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function set($name, $value = null)
    {
        if (is_string($name) &&
            in_array($name, ['timezone_type', 'date', 'timezone'], true)
        ) {
            $this->{$name} = $value;

            return $this;
        }

        return parent::set($name, $value);
    }
}
