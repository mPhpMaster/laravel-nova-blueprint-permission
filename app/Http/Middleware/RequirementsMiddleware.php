<?php

namespace App\Http\Middleware;

use Closure;
/**
 *
 */
class RequirementsMiddleware
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
        // $logo = config('brand.logo');
        //
        // if( !empty($logo) ) {
        //     throw_unless(file_exists(realpath($logo)), new FileNotFoundException("Brand logo [{$logo}] not exist!"));
        // }

        return $next($request);
    }
}
