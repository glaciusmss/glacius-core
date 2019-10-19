<?php

namespace App\Enums\Woocommerce;

use BenSampo\Enum\Enum;

/**
 * @method static WebhookTopic OrderCreate()
 * @method static WebhookTopic CustomerCreate()
 * @method static WebhookTopic CustomerUpdate()
 * @method static WebhookTopic CustomerDelete()
 */
final class WebhookTopic extends Enum
{
    const OrderCreate = 'order.created';

    const CustomerCreate = 'customer.created';
    const CustomerUpdate = 'customer.updated';
    const CustomerDelete = 'customer.deleted';
}
