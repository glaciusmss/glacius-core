<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 2/27/2020
 * Time: 3:48 PM.
 */

namespace App\Botman;


use App\Jobs\Bot\ReplyJob;
use Illuminate\Support\Arr;

trait BotAuthTrait
{
    public function validateAuth()
    {
        $shouldStop = $this->bot->getMessage()->getExtras('should_stop');
        if ($shouldStop) {
            $stopMsg = $this->bot->getMessage()->getExtras('stop_msg');
            foreach (Arr::wrap($stopMsg) as $msg) {
                ReplyJob::dispatch($this->bot, $msg);
            }
            return false;
        }

        return true;
    }
}
