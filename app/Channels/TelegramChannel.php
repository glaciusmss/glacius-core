<?php

namespace App\Channels;

use App\Jobs\Bot\SayJob;
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

        SayJob::dispatch($message, $toBotId, TelegramDriver::class);
    }
}
