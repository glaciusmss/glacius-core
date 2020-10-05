<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/30/2019
 * Time: 9:07 AM.
 */

namespace App\Botman\Controllers\Commands;

use App\Contracts\BotConnector;
use App\Enums\ServiceMethod;
use App\Jobs\Bot\ReplyJob;
use App\Services\Connectors\BotAuthManager;
use App\Services\Connectors\ConnectorManager;

class DisconnectCommand extends BaseCommand
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

        $disconnectedUser = $botAuthService->disconnect();

        ReplyJob::dispatch($this->bot, 'Successfully disconnected to Glacius with '.$disconnectedUser->email);
    }
}
