<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
	        \Badinansoft\LanguageSwitch\Http\Middleware\LanguageSwitch::class,
	        \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
	    if($exceptions instanceof \Illuminate\Validation\ValidationException) {
		    // return response()->json([
		    //                             'message' => $exception->getMessage(),
		    //                         ], 401);
	    }
    })->create();
