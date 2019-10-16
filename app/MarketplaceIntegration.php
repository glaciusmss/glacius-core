<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\MarketplaceIntegration
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MarketplaceIntegration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MarketplaceIntegration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\MarketplaceIntegration query()
 * @mixin \Eloquent
 */
class MarketplaceIntegration extends Pivot
{
    protected $casts = [
        'meta' => 'array'
    ];
}
