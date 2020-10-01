<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 5:16 PM.
 */

namespace App\Services\NotificationChannels\Facebook;

use App\Contracts\BotAuth as BotAuthContract;
use App\Enums\NotificationChannelEnum;
use App\Enums\TokenType;
use App\Exceptions\BotException;
use App\Services\NotificationChannels\BaseNotificationChannel;
use App\Models\Token;
use App\Models\User;

class BotAuth extends BaseNotificationChannel implements BotAuthContract
{
    //dont use laravel constructor binding,
    //will cause serialize issue

    public function connect($token)
    {
        $token = Token::validateAndDelete(trim($token), TokenType::FacebookConnect());

        throw_unless(
            $token,
            new BotException('Invalid token, please regenerate')
        );

        $user = User::find($token->meta['user_id']);

        $this->getNotificationChannel()->users()->attach($user, [
            'meta->'.$this->getBotIdKey() => $this->getBot()->getUser()->getId(),
            'meta->'.$this->getBotFirstNameKey() => $this->getBot()->getUser()->getFirstName(),
            'meta->'.$this->getBotLastNameKey() => $this->getBot()->getUser()->getLastName(),
        ]);

        return $user;
    }

    public function disconnect()
    {
        $this->getNotificationChannel()
            ->users()
            ->detach(\Auth::user());

        return \Auth::user();
    }

    public function name()
    {
        return NotificationChannelEnum::Facebook();
    }

    public function isBotConnectedToUser($botId)
    {
        return $this->getNotificationChannel()->users()
            ->wherePivot('meta->'.$this->getBotIdKey(), $botId)
            ->first(['email']);
    }
}
