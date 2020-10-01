<?php

namespace App;

use BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry as BaseWebSocketsStatisticsEntry;

class WebSocketsStatisticsEntry extends BaseWebSocketsStatisticsEntry
{
    public function __construct(array $attributes = [])
    {
        $this->connection = config('database.websocket_connection');

        parent::__construct($attributes);
    }
}
