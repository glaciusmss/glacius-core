<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static SyncDirection To()
 * @method static SyncDirection From()
 */
final class SyncDirection extends Enum
{
    const To = 0;
    const From = 1;
}
