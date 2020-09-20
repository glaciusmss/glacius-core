<?php


namespace App\Utils;


use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\Statistics\Logger\HttpStatisticsLogger;
use function GuzzleHttp\Psr7\stream_for;

class WebsocketStatisticsLogger extends HttpStatisticsLogger
{
    public function save()
    {
        foreach ($this->statistics as $appId => $statistic) {
            if (! $statistic->isEnabled()) {
                continue;
            }

            $postData = array_merge($statistic->toArray(), [
                'secret' => App::findById($appId)->secret,
            ]);

            $this
                ->browser
                ->post(
                    config('internal-url.app_url').'/'.config('websockets.path').'/statistics',
                    ['Content-Type' => 'application/json'],
                    stream_for(json_encode($postData))
                );

            $currentConnectionCount = $this->channelManager->getConnectionCount($appId);
            $statistic->reset($currentConnectionCount);
        }
    }
}
