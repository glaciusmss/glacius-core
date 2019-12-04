<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static DeviceType Web()
 * @method static DeviceType Mobile()
 */
final class DeviceType extends Enum
{
    const Web =   0;
    const Mobile =   1;
}
