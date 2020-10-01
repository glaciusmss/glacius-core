<?php

namespace App\Http\Middleware;

use App\Services\Connectors\ConnectorResolver;
use Closure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CheckSupportedMarketplace
{
    protected $connectorResolver;

    public function __construct(ConnectorResolver $connectorResolver)
    {
        $this->connectorResolver = $connectorResolver;
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
        if ($identifier = $request->route('identifier')) {
            if (! $this->connectorResolver->getAllIdentifiers()->contains($identifier)) {
                throw new BadRequestHttpException($identifier.' is not supported');
            }
        }

        return $next($request);
    }
}
