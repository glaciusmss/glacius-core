<?php

namespace App\Models;

use App\Models\MorphHelper\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\UserShop
 *
 * @property int $id
 * @property int $user_id
 * @property int $shop_id
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Setting[] $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserShop whereUserId($value)
 * @mixin \Eloquent
 */
class UserShop extends Pivot
{
    // this setting is used to control user setting for specific shop
    use HasSettings, HasFactory;

    protected $guarded = [];

    protected $table = 'user_shops';
}
