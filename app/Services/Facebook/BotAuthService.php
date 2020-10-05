<?php


namespace App\Services\Facebook;


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
        $token = Token::validateAndDelete(trim($token), TokenType::FacebookConnect());

        throw_unless(
            $token,
            new BotException('Invalid token, please regenerate')
        );

        $user = User::find($token->meta['user_id']);

        $this->getNotificationChannel('facebook')->users()->attach($user, [
            'meta->facebook_bot_id' => $bot->getUser()->getId(),
            'meta->facebook_bot_firstname' => $bot->getUser()->getFirstName(),
            'meta->facebook__bot_lastname' => $bot->getUser()->getLastName(),
        ]);

        return $user;
    }

    public function disconnect(BotMan $bot)
    {
        $this->getNotificationChannel('facebook')
            ->users()
            ->detach(\Auth::user());

        return \Auth::user();
    }

    public function isBotConnectedToUser(BotMan $bot, $botId)
    {
        return $this->getNotificationChannel('facebook')->users()
            ->wherePivot('meta->facebook_bot_id', $botId)
            ->first(['email']);
    }
}
