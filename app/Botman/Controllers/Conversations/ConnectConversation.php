<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 9:55 AM.
 */

namespace App\Botman\Controllers\Conversations;


use App\Contracts\BotAuth;
use App\Exceptions\BotException;
use BotMan\BotMan\Messages\Incoming\Answer;

class ConnectConversation extends BaseConversation
{
    protected $botAuth;

    public function __construct(BotAuth $botAuth)
    {
        $this->botAuth = $botAuth;
    }

    public function handle()
    {
        $this->bot->reply('Hello, I notice that you are not came from Glacius website.');
        $this->askForToken();
    }

    protected function askForToken()
    {
        $this->ask('Please send me the token you got from Glacius website.', function (Answer $answer) {
            try {
                $connectedUser = $this->botAuth->connect(
                    $answer->getText()
                );

                $this->say('Successfully connected to Glacius with ' . $connectedUser->email);
            } catch (BotException $exception) {
                app(\App\Exceptions\BotHandler::class)->render($exception, $this->bot);
                $this->askForToken();
            }
        });
    }
}
