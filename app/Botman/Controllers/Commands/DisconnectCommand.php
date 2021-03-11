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
use App\Services\Connectors\ManagerBuilder;

class DisconnectCommand extends BaseCommand
{
    protected $managerBuilder;

    public function __construct(ManagerBuilder $managerBuilder)
    {
        $this->managerBuilder = $managerBuilder;
    }

    public function handle()
    {
        /** @var BotAuthManager $botAuthManager */
        $botAuthManager = $this->managerBuilder
            ->setIdentifier($this->platform)
            ->setConnectorType(BotConnector::class)
            ->setManagerClass(BotAuthManager::class)
            ->setServiceMethod(ServiceMethod::BotAuthService)
            ->build();

        $botAuthManager->setBot($this->bot);

        $disconnectedUser = $botAuthManager->disconnect();

        ReplyJob::dispatch($this->bot, 'Successfully disconnected to Glacius with '.$disconnectedUser->email);
    }
}
