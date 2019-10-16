<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Collection;
use Throwable;

class BotException extends Exception
{
    public $messages;

    public function __construct($messages = [], $code = 0, Throwable $previous = null)
    {
        $this->messages = Collection::wrap($messages);

        parent::__construct($this->messages->first(), $code, $previous);
    }
}
