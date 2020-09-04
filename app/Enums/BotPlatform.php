<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static BotPlatform Telegram()
 * @method static BotPlatform Facebook()
 */
final class BotPlatform extends Enum
{
    const Telegram = 'telegram';
    const Facebook = 'facebook';
}
