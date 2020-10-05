<?php


namespace App\Services\Connectors;


use App\Contracts\BotAuth;
use App\Models\User;
use BotMan\BotMan\BotMan;

class BotAuthManager
{
    protected $botAuthService;
    protected $bot;

    public function __construct(BotAuth $service)
    {
        $this->botAuthService = $service;
    }

    public function connect(string $token): User
    {
        return $this->botAuthService->connect($this->bot, $token);
    }

    public function disconnect(): User
    {
        return $this->botAuthService->disconnect($this->bot);
    }

    public function isBotConnectedToUser($botId)
    {
        return $this->botAuthService->isBotConnectedToUser($this->bot, $botId);
    }

    public function setBot(BotMan $bot): self
    {
        $this->bot = $bot;
        return $this;
    }
}
