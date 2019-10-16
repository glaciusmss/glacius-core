<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static ProcessorType Order()
 * @method static ProcessorType Customer()
 */
final class ProcessorType extends Enum
{
    const Order = 0;
    const Customer = 1;
}
