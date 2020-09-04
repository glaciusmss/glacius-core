<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static TokenType TelegramConnect()
 * @method static TokenType FacebookConnect()
 */
final class TokenType extends Enum
{
    const TelegramConnect = 0;
    const FacebookConnect = 1;
}
