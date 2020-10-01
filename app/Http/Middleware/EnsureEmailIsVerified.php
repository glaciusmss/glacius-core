<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/25/2019
 * Time: 12:59 PM.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail())) {
            throw new AccessDeniedHttpException('your email address is not verified.');
        }

        return $next($request);
    }
}
