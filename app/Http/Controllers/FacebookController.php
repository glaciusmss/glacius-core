<?php

namespace App\Http\Controllers;

use App\Contracts\BotConnector;
use App\Enums\ServiceMethod;
use App\Enums\TokenType;
use App\Models\Token;
use App\Services\Connectors\BotAuthManager;
use App\Services\Connectors\ManagerBuilder;

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

    public function disconnect(ManagerBuilder $managerBuilder)
    {
        /** @var BotAuthManager $botAuthManager */
        $botAuthManager = $managerBuilder
            ->setIdentifier('facebook')
            ->setConnectorType(BotConnector::class)
            ->setManagerClass(BotAuthManager::class)
            ->setServiceMethod(ServiceMethod::BotAuthService)
            ->build();

        $botAuthManager->setBot(app('botman'));

        $botAuthManager->disconnect();

        return response()->noContent();
    }
}
