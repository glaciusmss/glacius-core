<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperMarketplaceIntegration
 */
class MarketplaceIntegration extends Pivot
{
    protected $casts = [
        'meta' => 'array'
    ];
}
