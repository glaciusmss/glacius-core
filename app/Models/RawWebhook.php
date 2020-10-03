<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RawWebhook
 *
 * @property int $id
 * @property array $raw_data
 * @property string|null $topic
 * @property int $marketplace_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \App\Models\Marketplace $marketplace
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook whereTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RawWebhook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RawWebhook extends Model
{
    protected $guarded = [];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
