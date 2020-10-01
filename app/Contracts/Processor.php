<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/20/2019
 * Time: 10:09 AM.
 */

namespace App\Contracts;

use App\Events\Webhook\WebhookReceived;

interface Processor
{
    public function onCreate(WebhookReceived $event);

    public function onUpdate(WebhookReceived $event);

    public function onDelete(WebhookReceived $event);

    public function processFor(): string;
}
