<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static EventType Created()
 * @method static EventType Updated()
 * @method static EventType Deleted()
 */
final class EventType extends Enum
{
    const Created = 0;
    const Updated = 1;
    const Deleted = 2;
}
