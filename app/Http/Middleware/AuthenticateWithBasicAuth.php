<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as BaseBasicAuth;

class AuthenticateWithBasicAuth extends BaseBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @param string|null $field
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        return parent::handle($request, $next, 'web');
    }
}
