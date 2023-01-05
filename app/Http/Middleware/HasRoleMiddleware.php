<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

/**
 *
 */
class HasRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User $user */
        throw_if(!($user = $request->user()) || !$user->hasRole(...$roles), new \Illuminate\Validation\UnauthorizedException("Unauthorized.",403));

        return $next($request);
    }
}
