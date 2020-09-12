<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/27/2019
 * Time: 11:01 PM.
 */

namespace App\Exceptions;


use App\Jobs\Bot\ReplyJob;
use BotMan\BotMan\BotMan;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BotHandler
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function report(\Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (is_callable($reportCallable = [$e, 'report'])) {
            return $this->container->call($reportCallable);
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (\Exception $ex) {
            throw $e;
        }

        $logger->error(
            $e->getMessage(),
            array_merge($this->context(), ['exception' => $e])
        );
    }

    public function render(\Exception $exception, Botman $bot)
    {
        if ($exception instanceof BotException) {
            $exception->messages->each(function ($message) use ($bot) {
                ReplyJob::dispatch($bot, $message);
            });
        } else {
            ReplyJob::dispatch($bot, $exception->getMessage());
        }

        if (method_exists($bot->getDriver(), 'messagesHandled')) {
            $bot->getDriver()->messagesHandled();
        }
    }

    /**
     * Get the default context variables for logging.
     *
     * @return array
     */
    protected function context()
    {
        try {
            return array_filter([
                'userId' => \Auth::id(),
                // 'email' => optional(Auth::user())->email,
            ]);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param \Exception $e
     * @return bool
     */
    protected function shouldntReport(\Exception $e)
    {
        $dontReport = [
            HttpException::class,
            BotException::class,
        ];

        return !is_null(Arr::first($dontReport, function ($type) use ($e) {
            return $e instanceof $type;
        }));
    }
}
