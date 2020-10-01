<?php

namespace App\Http\Controllers;

use App\Contracts\BotAuth;
use App\Enums\TokenType;
use App\Token;
use Illuminate\Auth\AuthManager;

class TelegramController extends Controller
{
    protected $botAuth;

    public function __construct(AuthManager $auth, BotAuth $botAuth)
    {
        parent::__construct($auth);
        $this->botAuth = $botAuth;
    }

    public function connect()
    {
        $token = Token::generateAndSave(TokenType::TelegramConnect(), ['user_id' => $this->auth->id()]);

        return response()->json([
            'url' => "https://t.me/GlaciusBot?start={$token->token}",
            'token' => $token->token,
        ]);
    }

    public function disconnect()
    {
        $this->botAuth->disconnect();

        return response()->noContent();
    }
}
