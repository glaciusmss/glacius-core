<?php

namespace App\Notifications\Order;

use App\Channels\FacebookChannel;
use App\Channels\FcmChannel;
use App\Channels\TelegramChannel;
use App\Enums\FirebaseChannelEnum;
use App\Enums\QueueGroup;
use App\Http\Resources\OrderResource;
use App\Order;
use App\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class OrderCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /* @var Order $order */
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
        $this->onQueue(QueueGroup::Notification);
    }

    public function via($notifiable)
    {
        return [FacebookChannel::class, TelegramChannel::class, FcmChannel::class, 'broadcast'];
    }

    public function toTelegram($notifiable)
    {
        /** @var Shop $notifiable */
        return $notifiable->name . ': You have new order, ID [' . $this->order->id . '] from [' . Str::title($this->order->marketplace->name) . ']';
    }

    public function toFacebook($notifiable)
    {
        /** @var Shop $notifiable */
        return $notifiable->name . ': You have new order, ID [' . $this->order->id . '] from [' . Str::title($this->order->marketplace->name) . ']';
    }

    public function toBroadcast($notifiable)
    {
        $message = new BroadcastMessage([
            'order' => new OrderResource($this->order)
        ]);

        return $message->onQueue(QueueGroup::Broadcast);
    }

    public function toFcm($notifiable)
    {
        return [
            'options' => (new OptionsBuilder())
                ->setPriority('high'),

            'notification' => (new PayloadNotificationBuilder())
                ->setChannelId(FirebaseChannelEnum::Default) // for android > 26
                ->setTitle($notifiable->name)
                ->setBody('You have new order, ID [' . $this->order->id . '] from [' . Str::title($this->order->marketplace->name) . ']'),

            'data' => (new PayloadDataBuilder())
                ->setData([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'model' => 'order',
                    'model_id' => $this->order->id,
                ])
        ];
    }
}
