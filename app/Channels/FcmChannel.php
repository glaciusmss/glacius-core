<?php

namespace App\Channels;

use App\Shop;
use FCM;
use Illuminate\Notifications\Notification;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;

class FcmChannel
{
    public function send($notifiable, Notification $notification)
    {
//        if (!$deviceId = $notifiable->routeNotificationFor('fcm', $notification)) {
//            return;
//        }

        $message = $notification->toFcm($notifiable);

        if (! is_array($message)) {
            return;
        }

        $options = $this->buildOptionsFromMessage($message);
        $notificationMessage = $this->buildNotificationFromMessage($message);
        $dataMessage = $this->buildDataFromMessage($message);

        FCM::sendToTopic(
            $this->getTopic($notifiable),
            $options,
            $notificationMessage,
            $dataMessage
        );
    }

    protected function getTopic($notifiable)
    {
        /** @var Shop $notifiable */
        $class = str_replace('\\', '.', get_class($notifiable));

        $topic = $class.'.'.$notifiable->getKey();

        return (new Topics())->topic($topic);
    }

    protected function buildOptionsFromMessage($message)
    {
        if (! isset($message['options'])) {
            return null;
        }

        if ($message['options'] instanceof OptionsBuilder) {
            return $message['options']->build();
        }

        return $message['options'];
    }

    protected function buildNotificationFromMessage($message)
    {
        if (! isset($message['notification'])) {
            return null;
        }

        if ($message['notification'] instanceof PayloadNotificationBuilder) {
            return $message['notification']->build();
        }

        return $message['notification'];
    }

    protected function buildDataFromMessage($message)
    {
        if (! isset($message['data'])) {
            return null;
        }

        if ($message['data'] instanceof PayloadDataBuilder) {
            return $message['data']->build();
        }

        return $message['data'];
    }
}
