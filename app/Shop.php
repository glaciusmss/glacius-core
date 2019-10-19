<?php

namespace App;

use App\Enums\NotificationChannelEnum;
use App\Utils\HasSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * App\Shop
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Marketplace[] $marketplaces
 * @property-read int|null $marketplaces_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Shop whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Setting[] $settings
 * @property-read int|null $settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Customer[] $customers
 * @property-read int|null $customers_count
 */
class Shop extends Model
{
    use Notifiable, HasSettings;

    protected $fillable = [
        'name', 'description'
    ];

    protected $settingTypes = [
        'is_product_sync_activated' => 'boolean',
    ];

    public function routeNotificationForTelegram()
    {
        $botIds = [];

        $notificationChannels = $this->user->notificationChannels()
            ->where('notification_channels.name', NotificationChannelEnum::Telegram)
            ->get();

        foreach ($notificationChannels as $notificationChannel) {
            $botIds[] = $notificationChannel->pivot->meta['telegram_bot_id'];
        }

        return $botIds;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
            ->withPivot(['token', 'refreshToken', 'meta'])
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
}
