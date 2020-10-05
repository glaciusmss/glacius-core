<?php


namespace App\Services\Telegram;

use App\Contracts\BotConnector;

class TelegramConnector implements BotConnector
{
    public function getConnectorIdentifier(): string
    {
        return 'telegram';
    }

    public function getBotAuthService()
    {
        return BotAuthService::class;
    }
}
