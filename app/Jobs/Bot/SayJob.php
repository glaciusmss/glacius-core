<?php


namespace App\Jobs\Bot;

use App\Enums\QueueGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $message;
    protected $recipient;
    protected $driver;

    public function __construct($message, $recipient, $driver)
    {
        $this->message = $message;
        $this->recipient = $recipient;
        $this->driver = $driver;
        $this->onQueue(QueueGroup::Bot);
    }

    public function handle()
    {
        app('botman')->say(
            $this->message,
            $this->recipient,
            $this->driver,
        );
    }
}
