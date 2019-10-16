<?php

namespace App\Notifications\Order;

use App\Channels\TelegramChannel;
use App\Enums\QueueGroup;
use App\Http\Resources\OrderResource;
use App\Order;
use App\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

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
        return [TelegramChannel::class, 'broadcast'];
    }

    public function toTelegram($notifiable)
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
}
