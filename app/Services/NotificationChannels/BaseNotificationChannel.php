<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 2:46 PM.
 */

namespace App\Services\NotificationChannels;


use App\Contracts\NotificationChannel;
use App\Enums\NotificationChannelEnum;

abstract class BaseNotificationChannel implements NotificationChannel
{
    use HasBotColumnKey;

    protected $notificationChannel;
    protected $bot;

    /**
     * @return \App\NotificationChannel
     */
    protected function getNotificationChannel()
    {
        if ($this->notificationChannel) {
            return $this->notificationChannel;
        }

        $name = ($this->name() instanceof NotificationChannelEnum) ? $this->name()->key : $this->name();

        return $this->notificationChannel = \App\NotificationChannel::whereName($name)->first();
    }

    protected function getBot()
    {
        if ($this->bot) {
            return $this->bot;
        }

        return $this->bot = app('botman');
    }
}
