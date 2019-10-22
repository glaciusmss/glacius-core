<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static SocialProvider Google()
 * @method static SocialProvider Facebook()
 */
final class SocialProvider extends Enum
{
    const Google = 'google';
    const Facebook = 'facebook';
}
