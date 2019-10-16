<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static State Success()
 * @method static State Error()
 * @method static State Pending()
 */
final class State extends Enum
{
    const Success = 0;
    const Error = 1;
    const Pending = 2;
}
