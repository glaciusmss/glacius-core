<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;

trait HasNotificationChannel
{
    protected $notificationChannel;

    /**
     * @param string $name
     * @return \App\Models\NotificationChannel
     */
    protected function getNotificationChannel(string $name)
    {
        if ($this->notificationChannel) {
            return $this->notificationChannel;
        }

        return $this->notificationChannel = \App\Models\NotificationChannel::whereName($name)->first();
    }
}
