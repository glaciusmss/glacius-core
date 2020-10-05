<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 5:16 PM.
 */

namespace App\Contracts;

use BotMan\BotMan\BotMan;

interface BotAuth
{
    public function connect(BotMan $bot, string $token);

    public function disconnect(BotMan $bot);

    public function isBotConnectedToUser(BotMan $bot, $botId);
}
