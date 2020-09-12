<?php

use BotMan\BotMan\BotMan;

$botman = app('botman');

$botman->hears('/start{connectToken}', 'App\Botman\Controllers\Commands\StartCommand@run');
$botman->hears('/disconnect', 'App\Botman\Controllers\Commands\DisconnectCommand@run');

//facebook deep link
$botman->on('messaging_referrals', function ($payload, $bot) {
    app()->make(\App\Botman\Controllers\Commands\StartCommand::class)->handleFbMessenger($payload, $bot);
});

$botman->fallback(function (Botman $bot) {
    \App\Jobs\Bot\TypingJob::dispatch($bot);
    \App\Jobs\Bot\ReplyJob::dispatch($bot, 'sorry, command still in development stages');
});

$botman->exception(\Exception::class, function (\Exception $exception, Botman $bot) {
    $exceptionHandler = app(App\Exceptions\BotHandler::class);
    $exceptionHandler->report($exception);
    return $exceptionHandler->render($exception, $bot);
});
