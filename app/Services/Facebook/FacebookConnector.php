<?php


namespace App\Services\Facebook;

use App\Contracts\BotConnector;

class FacebookConnector implements BotConnector
{
    public function getConnectorIdentifier(): string
    {
        return 'facebook';
    }

    public function getBotAuthService()
    {
        return BotAuthService::class;
    }
}
