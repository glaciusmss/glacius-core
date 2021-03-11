<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static ServiceMethod AuthService()
 * @method static ServiceMethod BotAuthService()
 * @method static ServiceMethod WebhookService()
 * @method static ServiceMethod SyncService()
 * @method static ServiceMethod ProcessorService()
 */
final class ServiceMethod extends Enum
{
    const AuthService = 'getAuthService';
    const BotAuthService = 'getBotAuthService';
    const WebhookService = 'getWebhookService';
    const SyncService = 'getSyncService';
    const ProcessorService = 'getProcessorServices';
}
