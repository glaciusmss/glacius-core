<?php

namespace App\Models;

use App\Enums\NotificationChannelEnum;
use App\Scopes\OrderScope;
use App\Models\MorphHelper\HasSettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * App\Models\Shop
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \App\Utils\CarbonFix|null $created_at
 * @property \App\Utils\CarbonFix|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Marketplace[] $marketplaces
 * @property-read int|null $marketplaces_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Setting[] $settings
 * @property-read int|null $settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Shop extends Model
{
    use Notifiable, HasSettings, OrderScope, HasRelationships, HasFactory;

    protected $guarded = [];

    protected $settingTypes = [
        'is_product_sync_activated' => 'boolean',
    ];

    public function routeNotificationForTelegram()
    {
        $botIds = [];

        $notificationChannels = $this->userNotificationChannels()
            ->where('notification_channels.name', NotificationChannelEnum::Telegram)
            ->get();

        foreach ($notificationChannels as $notificationChannel) {
            $botIds[] = $notificationChannel->pivot->meta['telegram_bot_id'];
        }

        return $botIds;
    }

    public function routeNotificationForFacebook()
    {
        $botIds = [];

        $notificationChannels = $this->userNotificationChannels()
            ->where('notification_channels.name', NotificationChannelEnum::Facebook)
            ->get();

        foreach ($notificationChannels as $notificationChannel) {
            $botIds[] = $notificationChannel->pivot->meta['facebook_bot_id'];
        }

        return $botIds;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_shops')
            ->withTimestamps()
            ->using(UserShop::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return Marketplace|\Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function marketplaces()
    {
        return $this->belongsToMany(Marketplace::class, 'marketplace_integrations')
            ->withTimestamps()
            ->withPivot(['id', 'token', 'refreshToken', 'meta'])
            ->using(MarketplaceIntegration::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function userNotificationChannels()
    {
        return $this->hasManyDeep(NotificationChannel::class, ['user_shops', User::class, 'connected_notification_channels'])
            ->withPivot('connected_notification_channels', ['meta', 'created_at', 'updated_at'], ConnectedNotificationChannel::class, 'pivot');
    }
}
