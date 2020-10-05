<?php

namespace App\Http\Controllers;

use App\Contracts\BotConnector;
use App\Enums\ServiceMethod;
use App\Enums\TokenType;
use App\Models\Token;
use App\Services\Connectors\BotAuthManager;
use App\Services\Connectors\ConnectorManager;

class FacebookController extends Controller
{
    public function connect()
    {
        $token = Token::generateAndSave(TokenType::FacebookConnect(), ['user_id' => $this->auth->id()]);

        return response()->json([
            'url' => "https://m.me/glaciusmss?ref={$token->token}",
            'token' => $token->token,
        ]);
    }

    public function disconnect(ConnectorManager $connectorManager)
    {
        $botAuthService = $connectorManager->getServiceManager(
            'facebook',
            BotConnector::class,
            BotAuthManager::class,
            ServiceMethod::BotAuthService
        )->setBot(app('botman'));

        $botAuthService->disconnect();

        return response()->noContent();
    }
}
