<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 5:03 PM.
 */

namespace App\Http\Middleware;

use App\Contracts\Webhook;
use App\Enums\ServiceMethod;
use App\Services\Connectors\ManagerBuilder;
use App\Services\Connectors\WebhookManager;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ValidateWebhook
{
    protected $managerBuilder;

    public function __construct(ManagerBuilder $managerBuilder)
    {
        $this->managerBuilder = $managerBuilder;
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
        $identifier = $request->route('identifier');

        /** @var WebhookManager $webhookManager */
        $webhookManager = $this->managerBuilder
            ->setIdentifier($identifier)
            ->setManagerClass(WebhookManager::class)
            ->setServiceMethod(ServiceMethod::WebhookService)
            ->build();

        if (! $webhookManager->validateHmac($request)) {
            throw new AccessDeniedHttpException('invalid token');
        }

        return $next($request);
    }
}
