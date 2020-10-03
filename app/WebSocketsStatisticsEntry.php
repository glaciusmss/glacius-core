<?php

namespace App;

use BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry as BaseWebSocketsStatisticsEntry;

/**
 * App\WebSocketsStatisticsEntry
 *
 * @property int $id
 * @property string $app_id
 * @property int $peak_connection_count
 * @property int $websocket_message_count
 * @property int $api_message_count
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry whereApiMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry wherePeakConnectionCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WebSocketsStatisticsEntry whereWebsocketMessageCount($value)
 * @mixin \Eloquent
 */
class WebSocketsStatisticsEntry extends BaseWebSocketsStatisticsEntry
{
    public function __construct(array $attributes = [])
    {
        $this->connection = config('database.websocket_connection');

        parent::__construct($attributes);
    }
}
