<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 9:55 AM.
 */

namespace App\Botman\Controllers\Conversations;

use App\Contracts\BotConnector;
use App\Enums\ServiceMethod;
use App\Exceptions\BotException;
use App\Jobs\Bot\ReplyJob;
use App\Services\Connectors\BotAuthManager;
use App\Services\Connectors\ManagerBuilder;
use BotMan\BotMan\Messages\Incoming\Answer;

class ConnectConversation extends BaseConversation
{
    protected $managerBuilder;

    public function __construct(ManagerBuilder $managerBuilder)
    {
        $this->managerBuilder = $managerBuilder;
    }

    public function handle()
    {
        ReplyJob::dispatch($this->bot, 'Hello, I notice that you are not came from Glacius website.');
        $this->askForToken();
    }

    protected function askForToken()
    {
        $this->queuedAsk('Please send me the token you got from Glacius website.', function (Answer $answer) {
            try {
                /** @var BotAuthManager $botAuthManager */
                $botAuthManager = $this->managerBuilder
                    ->setIdentifier($this->platform)
                    ->setConnectorType(BotConnector::class)
                    ->setManagerClass(BotAuthManager::class)
                    ->setServiceMethod(ServiceMethod::BotAuthService)
                    ->build();

                $botAuthManager->setBot($this->bot);

                $connectedUser = $botAuthManager->connect($answer->getText());

                ReplyJob::dispatch($this->bot, 'Successfully connected to Glacius with ' . $connectedUser->email);
            } catch (BotException $exception) {
                app(\App\Exceptions\BotHandler::class)->render($exception, $this->bot);
                $this->askForToken();
            }
        });
    }
}
