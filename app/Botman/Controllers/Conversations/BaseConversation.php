<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 9:55 AM.
 */

namespace App\Botman\Controllers\Conversations;

use App\Botman\BotAuthTrait;
use App\Jobs\Bot\ReplyJob;
use BotMan\BotMan\Messages\Conversations\Conversation;

abstract class BaseConversation extends Conversation
{
    use BotAuthTrait;

    protected $platform;

    public function run()
    {
        $this->platform = strtolower($this->bot->getDriver()->getName());

        if (! $this->validateAuth()) {
            return;
        }

        $this->handle();
    }

    protected function getUser()
    {
        return \Auth::user();
    }

    public function queuedAsk($question, $next, $additionalParameters = [])
    {
        ReplyJob::dispatch($this->bot, $question);
        $this->bot->storeConversation($this, $next, $question, $additionalParameters);

        return $this;
    }

    abstract public function handle();
}
