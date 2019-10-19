<?php

namespace App\Notifications\Customer;

use App\Channels\TelegramChannel;
use App\Customer;
use App\Enums\QueueGroup;
use App\Http\Resources\CustomerResource;
use App\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CustomerCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /* @var Customer $customer */
    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
        $this->onQueue(QueueGroup::Notification);
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class, 'broadcast'];
    }

    public function toTelegram($notifiable)
    {
        /** @var Shop $notifiable */
        return $notifiable->name . ': You have new customer, ID [' . $this->customer->id . '] from [' . Str::title($this->customer->marketplace->name) . ']';
    }

    public function toBroadcast($notifiable)
    {
        $message = new BroadcastMessage([
            'customer' => new CustomerResource($this->customer)
        ]);

        return $message->onQueue(QueueGroup::Broadcast);
    }
}
