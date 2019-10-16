<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 5:03 PM.
 */

namespace App\Http\Middleware\Webhook;

use App\Contracts\Webhook;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ValidateWebhook
{
    protected $webhook;

    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$this->webhook->validateHmac($request)) {
            throw new AccessDeniedHttpException('invalid token');
        }

        return $next($request);
    }
}
