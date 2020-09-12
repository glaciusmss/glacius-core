<?php

namespace App\Services\Easystore\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static WebhookTopic OrderCreate()
 * @method static WebhookTopic CustomerCreate()
 * @method static WebhookTopic CustomerDelete()
 */
final class WebhookTopic extends Enum
{
    const OrderCreate = 'order/create';

    const CustomerCreate = 'customer/create';
    const CustomerDelete = 'customer/delete';
}
