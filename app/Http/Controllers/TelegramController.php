<?php

namespace App\Http\Controllers;

use App\Contracts\BotConnector;
use App\Enums\ServiceMethod;
use App\Enums\TokenType;
use App\Models\Token;
use App\Services\Connectors\BotAuthManager;
use App\Services\Connectors\ConnectorManager;

class TelegramController extends Controller
{
    public function connect()
    {
        $token = Token::generateAndSave(TokenType::TelegramConnect(), ['user_id' => $this->auth->id()]);

        return response()->json([
            'url' => "https://t.me/GlaciusBot?start={$token->token}",
            'token' => $token->token,
        ]);
    }

    public function disconnect(ConnectorManager $connectorManager)
    {
        $botAuthService = $connectorManager->getServiceManager(
            'telegram',
            BotConnector::class,
            BotAuthManager::class,
            ServiceMethod::BotAuthService
        )->setBot(app('botman'));

        $botAuthService->disconnect();

        return response()->noContent();
    }
}
