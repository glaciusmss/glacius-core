<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 9:55 AM.
 */

namespace App\Botman\Controllers\Conversations;

use App\Enums\BotPlatform;
use BotMan\BotMan\Messages\Conversations\Conversation;

abstract class BaseConversation extends Conversation
{
    protected $platform;

    public function run()
    {
        $shouldStop = $this->bot->getMessage()->getExtras('should_stop');
        if ($shouldStop) {
            $this->bot->reply('you are not authenticated');
            return;
        }

        $this->platform = BotPlatform::coerce($this->bot->getDriver()->getName());

        $this->handle();
    }

    protected function getUser()
    {
        return \Auth::user();
    }

    abstract public function handle();
}
