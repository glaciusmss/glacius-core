<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 3:50 PM.
 */

namespace App\Botman\Controllers\Commands;


use App\Botman\Controllers\Conversations\ConnectConversation;
use App\Contracts\BotAuth;
use App\Jobs\Bot\ReplyJob;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Arr;

class StartCommand extends BaseCommand
{
    protected $botAuth;

    public function __construct(BotAuth $botAuth)
    {
        $this->botAuth = $botAuth;
    }

    public function handle()
    {
        if ($connectedUser = $this->botAuth->isBotConnectedToUser($this->bot->getUser()->getId())) {
            ReplyJob::dispatch($this->bot, 'This account has connected to ' . $connectedUser->email);
            ReplyJob::dispatch($this->bot, 'Please disconnect first before connect to another account');
            return;
        }

        if ($connectToken = $this->parameters->first()) {
            $connectedUser = $this->botAuth->connect($connectToken);
            ReplyJob::dispatch($this->bot, 'Successfully connected to Glacius with ' . $connectedUser->email);
            return;
        }

        $this->bot->startConversation(new ConnectConversation($this->botAuth));
    }

    public function handleFbMessenger($payload, BotMan $bot)
    {
        $this->bot = $bot;
        $this->parameters = Arr::wrap($payload['referral']['ref']);

        $this->handle();
    }
}
