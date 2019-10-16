<?php

namespace App\Channels;

use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!$toBotId = $notifiable->routeNotificationFor('telegram', $notification)) {
            return;
        }

        $message = $notification->toTelegram($notifiable);

        app('botman')->say($message, $toBotId, TelegramDriver::class);
    }
}
