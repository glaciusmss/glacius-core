<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 3:45 PM.
 */

namespace App\Botman\Controllers\Commands;

use App\Botman\BotAuthTrait;
use App\Enums\BotPlatform;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Collection;

abstract class BaseCommand
{
    use BotAuthTrait;

    protected $platform;
    /* @var BotMan $bot */
    protected $bot;
    /* @var Collection $parameters */
    protected $parameters;

    public function run(BotMan $bot, ...$parameters)
    {
        $this->bot = $bot;
        $this->parameters = collect($parameters);
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

    abstract public function handle();
}
