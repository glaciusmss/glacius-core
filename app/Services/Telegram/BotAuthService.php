<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 5:16 PM.
 */

namespace App\Services\Telegram;

use App\Contracts\BotAuth;
use App\Enums\TokenType;
use App\Exceptions\BotException;
use App\Models\Token;
use App\Models\User;
use App\Utils\HasNotificationChannel;
use BotMan\BotMan\BotMan;

class BotAuthService implements BotAuth
{
    use HasNotificationChannel;

    public function connect(BotMan $bot, string $token)
    {
        $token = Token::validateAndDelete(trim($token), TokenType::TelegramConnect());

        throw_unless(
            $token,
            new BotException('Invalid token, please regenerate')
        );

        $user = User::find($token->meta['user_id']);

        $this->getNotificationChannel('telegram')->users()->attach($user, [
            'meta->telegram_bot_id' => $bot->getUser()->getId(),
            'meta->telegram_bot_username' => $bot->getUser()->getUsername(),
        ]);

        return $user;
    }

    public function disconnect(BotMan $bot)
    {
        $this->getNotificationChannel('telegram')
            ->users()
            ->detach(\Auth::user());

        return \Auth::user();
    }

    public function isBotConnectedToUser(BotMan $bot, $botId)
    {
        return $this->getNotificationChannel('telegram')->users()
            ->wherePivot('meta->telegram_bot_id', $botId)
            ->first(['email']);
    }
}
