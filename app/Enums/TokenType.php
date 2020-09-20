<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static TokenType TelegramConnect()
 * @method static TokenType FacebookConnect()
 * @method static TokenType WoocommerceConnect()
 */
final class TokenType extends Enum
{
    const TelegramConnect = 0;
    const FacebookConnect = 1;
    const WoocommerceConnect = 2;
}
