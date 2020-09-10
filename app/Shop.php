<?php

namespace App;

use App\Enums\NotificationChannelEnum;
use App\Scopes\OrderScope;
use App\Utils\HasSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperShop
 */
class Shop extends Model
{
    use Notifiable, HasSettings, OrderScope;

    protected $fillable = [
        'name', 'description'
    ];

    protected $settingTypes = [
        'is_product_sync_activated' => 'boolean',
    ];

    public function routeNotificationForTelegram()
    {
        $botIds = [];

        foreach($this->users as $user) {
            $notificationChannels = $user->notificationChannels()
                ->where('notification_channels.name', NotificationChannelEnum::Telegram)
                ->get();

            foreach ($notificationChannels as $notificationChannel) {
                $botIds[] = $notificationChannel->pivot->meta['telegram_bot_id'];
            }
        }

        return $botIds;
    }

    public function routeNotificationForFacebook()
    {
        $botIds = [];

        foreach($this->users as $user) {
            $notificationChannels = $user->notificationChannels()
                ->where('notification_channels.name', NotificationChannelEnum::Facebook)
                ->get();

            foreach ($notificationChannels as $notificationChannel) {
                $botIds[] = $notificationChannel->pivot->meta['facebook_bot_id'];
            }
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
