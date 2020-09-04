<?php

namespace App\Channels;

use BotMan\Drivers\Facebook\FacebookDriver;
use Illuminate\Notifications\Notification;

class FacebookChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!$toBotId = $notifiable->routeNotificationFor('facebook', $notification)) {
            return;
        }

        $message = $notification->toFacebook($notifiable);

        app('botman')->say($message, $toBotId, FacebookDriver::class);
    }
}
