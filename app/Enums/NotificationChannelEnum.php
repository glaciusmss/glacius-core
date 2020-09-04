<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static NotificationChannelEnum Telegram()
 * @method static NotificationChannelEnum Facebook()
 */
final class NotificationChannelEnum extends Enum
{
    const Telegram = 'telegram';
    const Facebook = 'facebook';
}
