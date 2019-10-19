<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 10:00 AM.
 */

namespace App\Botman\Middleware;


use App\Enums\BotPlatform;
use App\NotificationChannel;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Illuminate\Support\Str;

class AuthenticateBotUser implements Received
{
    protected $excepts = [
        '/start'
    ];

    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        if (Str::startsWith($message->getText(), $this->excepts)) {
            return $next($message);
        }

        $platform = strtolower($bot->getDriver()->getName());
        $botId = $message->getSender();

        if (BotPlatform::Telegram()->is($platform)) {
            $channel = $this->getNotificationChannel($platform);
        }

        $userAssociated = optional($channel)->users()
            ->wherePivot("meta->{$platform}_bot_id", $botId)
            ->first();

        if (!$userAssociated) {
            $message->addExtras('should_stop', true);
            return $next($message);
        }

        \Auth::login($userAssociated);

        return $next($message);
    }

    protected function getNotificationChannel($name)
    {
        return NotificationChannel::whereName($name)->first();
    }
}
