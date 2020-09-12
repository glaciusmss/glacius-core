<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/30/2019
 * Time: 9:07 AM.
 */

namespace App\Botman\Controllers\Commands;


use App\Contracts\BotAuth;
use App\Jobs\Bot\ReplyJob;

class DisconnectCommand extends BaseCommand
{
    protected $botAuth;

    public function __construct(BotAuth $botAuth)
    {
        $this->botAuth = $botAuth;
    }

    public function handle()
    {
        $disconnectedUser = $this->botAuth->disconnect();
        ReplyJob::dispatch($this->bot, 'Successfully disconnected to Glacius with ' . $disconnectedUser->email);
    }
}
