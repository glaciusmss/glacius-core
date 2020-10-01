<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 5:16 PM.
 */

namespace App\Contracts;

interface BotAuth
{
    public function connect($token);

    public function disconnect();

    public function isBotConnectedToUser($botId);
}
