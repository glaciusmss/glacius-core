<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static QueueGroup Sync()
 * @method static QueueGroup Transaction()
 * @method static QueueGroup Broadcast()
 * @method static QueueGroup Notification()
 * @method static QueueGroup Webhook()
 * @method static QueueGroup Email()
 * @method static QueueGroup Bot()
 */
final class QueueGroup extends Enum
{
    const Sync = 'sync';
    const Transaction = 'transaction';
    const Broadcast = 'broadcast';
    const Notification = 'notification';
    const Webhook = 'webhook';
    const Email = 'email';
    const Bot = 'bot';
}
