<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Shop.{shop}', function ($user, App\Shop $shop) {
    //shop authentication has been resolve through binding,
    //means that when it reach here, the user is authenticated,
    //so we simply return true
    return true;
});
