<?php

use BotMan\BotMan\BotMan;

$botman = app('botman');

$botman->hears('/start{connectToken}', 'App\Botman\Controllers\Commands\StartCommand@run');
$botman->hears('/disconnect', 'App\Botman\Controllers\Commands\DisconnectCommand@run');

$botman->fallback(function (Botman $bot) {
    $bot->types();
    $bot->reply('sorry, command still in development stages');
});

$botman->exception(\Exception::class, function (\Exception $exception, Botman $bot) {
    $exceptionHandler = app(App\Exceptions\BotHandler::class);
    $exceptionHandler->report($exception);
    return $exceptionHandler->render($exception, $bot);
});
