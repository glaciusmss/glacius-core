<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRawWebhook
 */
class RawWebhook extends Model
{
    protected $fillable = [
        'raw_data', 'topic'
    ];

    protected $casts = [
        'raw_data' => 'array'
    ];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
