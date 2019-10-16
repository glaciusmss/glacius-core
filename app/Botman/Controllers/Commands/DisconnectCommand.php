<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/30/2019
 * Time: 9:07 AM.
 */

namespace App\Botman\Controllers\Commands;


use App\Contracts\BotAuth;
use BotMan\BotMan\BotMan;

class DisconnectCommand extends BaseCommand
{
    protected $botAuth;

    public function __construct(BotAuth $botAuth)
    {
        $this->botAuth = $botAuth;
    }

    public function handle(BotMan $bot)
    {
        $disconnectedUser = $this->botAuth->disconnect();
        $bot->reply('Successfully disconnected to Glacius with ' . $disconnectedUser->email);
    }
}
