<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static AddressType Default()
 * @method static AddressType Billing()
 * @method static AddressType Shipping()
 */
final class AddressType extends Enum
{
    const Default = 0;
    const Billing = 1;
    const Shipping = 2;
}
