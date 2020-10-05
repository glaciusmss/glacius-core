<?php


namespace App\Contracts;


interface BotConnector extends Connector
{
    /**
     * @return BotAuth|string authInstance|auth class
     */
    public function getBotAuthService();
}
