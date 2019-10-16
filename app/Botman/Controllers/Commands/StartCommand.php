<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 3:50 PM.
 */

namespace App\Botman\Controllers\Commands;


use App\Botman\Controllers\Conversations\ConnectConversation;
use App\Contracts\BotAuth;
use BotMan\BotMan\BotMan;

class StartCommand extends BaseCommand
{
    protected $botAuth;

    public function __construct(BotAuth $botAuth)
    {
        $this->botAuth = $botAuth;
    }

    public function handle(BotMan $bot, $connectToken)
    {
        if ($connectedUser = $this->botAuth->isBotConnectedToUser($bot->getUser()->getId())) {
            $bot->reply('This telegram account has connected to ' . $connectedUser->email);
            $bot->reply('Please disconnect first before connect to another account');
            return;
        }

        if ($connectToken) {
            $connectedUser = $this->botAuth->connect($connectToken);
            $bot->reply('Successfully connected to Glacius with ' . $connectedUser->email);
            return;
        }

        $bot->startConversation(new ConnectConversation($this->botAuth));
    }
}
