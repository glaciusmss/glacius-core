<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 9:55 AM.
 */

namespace App\Botman\Controllers\Conversations;

use App\Botman\BotAuthTrait;
use App\Enums\BotPlatform;
use BotMan\BotMan\Messages\Conversations\Conversation;

abstract class BaseConversation extends Conversation
{
    use BotAuthTrait;

    protected $platform;

    public function run()
    {
        $this->platform = BotPlatform::coerce($this->bot->getDriver()->getName());

        if (!$this->validateAuth()) {
            return;
        }

        $this->handle();
    }

    protected function getUser()
    {
        return \Auth::user();
    }

    abstract public function handle();
}
