<?php

namespace App\Http\Middleware;

use Closure;

/**
 *
 */
class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headerLang = trim(request()->header('Accept-Language') ?? getDefaultLocale());
        $headerLang = isLocaleAllowed($headerLang) ? $headerLang : getDefaultLocale('ar');
        setCurrentLocale($headerLang);


        return $next($request);
    }
}
