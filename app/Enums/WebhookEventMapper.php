<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static WebhookEventMapper Create()
 * @method static WebhookEventMapper Update()
 * @method static WebhookEventMapper Delete()
 */
final class WebhookEventMapper extends Enum
{
    const Create = 'onCreate';
    const Update = 'onUpdate';
    const Delete = 'onDelete';
}
