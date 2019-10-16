<?php

namespace App\Jobs\Bot;

use App\Enums\QueueGroup;
use BotMan\BotMan\BotMan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Opis\Closure\SerializableClosure;

class ReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $bot;
    protected $message;

    public function __construct(Botman $bot, $message)
    {
        $this->bot = new SerializableClosure(function () use ($bot) {
            return $bot;
        });
        $this->message = $message;
        $this->onQueue(QueueGroup::Bot);
    }

    public function handle()
    {
        $closure = $this->bot;
        $closure()->reply($this->message);
    }
}
