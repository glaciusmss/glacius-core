<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/30/2019
 * Time: 10:22 AM.
 */

namespace App\Services\NotificationChannels;


trait HasBotColumnKey
{
    protected function getBotIdKey()
    {
        return $this->name()->value . '_bot_id';
    }

    protected function getBotUsernameKey()
    {
        return $this->name()->value . '_bot_username';
    }

    protected function getBotFirstNameKey()
    {
        return $this->name()->value . '_bot_firstname';
    }

    protected function getBotLastNameKey()
    {
        return $this->name()->value . '_bot_lastname';
    }
}
