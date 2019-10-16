<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RawWebhook
 *
 * @property int $id
 * @property array $raw_data
 * @property int $marketplace_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Marketplace $marketplace
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook whereRawData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RawWebhook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RawWebhook extends Model
{
    protected $fillable = [
        'raw_data'
    ];

    protected $casts = [
        'raw_data' => 'array'
    ];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
