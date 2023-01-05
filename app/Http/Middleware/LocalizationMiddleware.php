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
        $headerLang = isLocaleAllowed($headerLang) ? $headerLang : null;
        // app()->setLocale($headerLang);
        setCurrentLocale($headerLang);

        // $headerLang = trim(request()->header('Accept-Language') ?? getDefaultLocale());
        // $headerLang = array_key_exists($headerLang, config("app.available_locales")) ? $headerLang : getDefaultLocale('en');
        // app()->setLocale($headerLang);
        // setCurrentLocale($headerLang);

        return $next($request);
    }
}
