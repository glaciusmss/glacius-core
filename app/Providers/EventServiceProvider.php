<?php

namespace App\Providers;

use App\Events\Customer\CustomerCreated;
use App\Events\MarketplaceSynced;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use App\Events\Order\OrderCreated;
use App\Events\Product\ProductCreated;
use App\Events\Product\ProductDeleted;
use App\Events\Product\ProductUpdated;
use App\Events\Webhook\WebhookReceived;
use App\Listeners\Customer\SendCustomerNotification;
use App\Listeners\Order\SendOrderNotification;
use App\Listeners\RemoveShopSetting;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SetupDefaultSetting;
use App\Listeners\Sync\AddTransaction;
use App\Listeners\Sync\Product\SyncProduct;
use App\Listeners\Webhook\ProcessWebhook;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        Event::listen(WebhookReceived::class, ProcessWebhook::class);

        Event::listen(OrderCreated::class, SendOrderNotification::class);

        Event::listen(ProductCreated::class, SyncProduct::class);
        Event::listen(ProductUpdated::class, SyncProduct::class);
        Event::listen(ProductDeleted::class, SyncProduct::class);

        Event::listen(CustomerCreated::class, SendCustomerNotification::class);

        Event::listen(MarketplaceSynced::class, AddTransaction::class);

        Event::listen(OAuthConnected::class, SetupDefaultSetting::class);
        Event::listen(OAuthDisconnected::class, RemoveShopSetting::class);
    }
}
