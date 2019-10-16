<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 3:45 PM.
 */

namespace App\Botman\Controllers\Commands;

use App\Enums\BotPlatform;
use BotMan\BotMan\BotMan;

abstract class BaseCommand
{
    protected $platform;

    public function run(BotMan $bot)
    {
        $shouldStop = $bot->getMessage()->getExtras('should_stop');
        if ($shouldStop) {
            $bot->reply('you are not authenticated');
            return;
        }

        $this->platform = BotPlatform::coerce($bot->getDriver()->getName());

        $this->handle(...func_get_args());
    }

    protected function getUser()
    {
        return \Auth::user();
    }
}
