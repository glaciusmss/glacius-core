<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 3:50 PM.
 */

namespace App\Botman\Controllers\Commands;

use App\Botman\Controllers\Conversations\ConnectConversation;
use App\Contracts\BotConnector;
use App\Enums\ServiceMethod;
use App\Jobs\Bot\ReplyJob;
use App\Services\Connectors\BotAuthManager;
use App\Services\Connectors\ConnectorManager;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Arr;

class StartCommand extends BaseCommand
{
    protected $connectorManager;

    public function __construct(ConnectorManager $connectorManager)
    {
        $this->connectorManager = $connectorManager;
    }

    public function handle()
    {
        $botAuthService = $this->connectorManager->getServiceManager(
            $this->platform,
            BotConnector::class,
            BotAuthManager::class,
            ServiceMethod::BotAuthService
        )->setBot($this->bot);

        if ($connectedUser = $botAuthService->isBotConnectedToUser($this->bot->getUser()->getId())) {
            ReplyJob::dispatch($this->bot, 'This account has connected to '.$connectedUser->email);
            ReplyJob::dispatch($this->bot, 'Please disconnect first before connect to another account');

            return;
        }

        if ($connectToken = $this->parameters->first()) {
            $connectedUser = $botAuthService->connect($connectToken);
            ReplyJob::dispatch($this->bot, 'Successfully connected to Glacius with '.$connectedUser->email);

            return;
        }

        $this->bot->startConversation(app(ConnectConversation::class));
    }

    public function handleFbMessenger($payload, BotMan $bot)
    {
        $this->bot = $bot;
        $this->parameters = Arr::wrap($payload['referral']['ref']);

        $this->handle();
    }
}
