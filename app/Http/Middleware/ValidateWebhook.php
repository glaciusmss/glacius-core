<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 5:03 PM.
 */

namespace App\Http\Middleware;

use App\Contracts\Webhook;
use App\Services\Connectors\ConnectorManager;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ValidateWebhook
{
    protected $connectorManager;

    public function __construct(ConnectorManager $connectorManager)
    {
        $this->connectorManager = $connectorManager;
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

        $connector = $this->connectorManager->resolveConnector($identifier);

        /** @var Webhook $webhookService */
        $webhookService = $this->connectorManager->makeService($connector->getWebhookService());

        if (!$webhookService->validateHmac($request)) {
            throw new AccessDeniedHttpException('invalid token');
        }

        return $next($request);
    }
}
