<?php

namespace App\Services\Shopify\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static WebhookTopic OrderCreate()
 * @method static WebhookTopic CustomerCreate()
 * @method static WebhookTopic CustomerUpdate()
 * @method static WebhookTopic CustomerDelete()
 */
final class WebhookTopic extends Enum
{
    const OrderCreate = 'orders/create';

    const CustomerCreate = 'customers/create';
    const CustomerUpdate = 'customers/update';
    const CustomerDelete = 'customers/delete';
}
