<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\MarketplaceIntegration
 *
 * @property int $id
 * @property string|null $token
 * @property string|null $refreshToken
 * @property array $meta
 * @property int $shop_id
 * @property int $marketplace_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration query()
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereMarketplaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MarketplaceIntegration whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MarketplaceIntegration extends Pivot
{
    protected $guarded = [];

    protected $table = 'marketplace_integrations';

    protected $casts = [
        'meta' => 'array'
    ];
}
