<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Current api version
     */
    const API_VERSION = "v1";
    /**
     *
     */
    const API_DIR_PATH = "routes/api";
    /**
     *
     */
    const API_FILENAME = "{API_VERSION}.php";

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $version = RouteServiceProvider::API_VERSION;
            Route::middleware('api')
                ->prefix("api/{$version}")
                 // ->name('api.')
                ->group($this->getApiRoutesPath($version));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Check for api routes file if exists
     *
     * @param string|null $version null = RouteServiceProvider::API_VERSION
     *
     * @return string
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getApiRoutesPath(?string $version = RouteServiceProvider::API_VERSION): string
    {
        $version = trim($version) ?: RouteServiceProvider::API_VERSION;
        $parseString = fn($string): string => str_ireplace("{API_VERSION}", $version, $string);

        $apiPath = base_path($parseString(RouteServiceProvider::API_DIR_PATH));
        if( !file_exists($apiPath) || !is_dir($apiPath) ) {
            $apiPath = base_path("routes");
        }

        $apiFilePath = $apiPath . "/" . $parseString(RouteServiceProvider::API_FILENAME);
        if( !file_exists($apiFilePath) ) {
            $apiFilePath = $apiPath . "/api.php";

            if( !file_exists($apiFilePath) ) {
                $apiFilePath = base_path("routes/api.php");
            }
        }

        abort_if(!$apiFilePath || !file_exists($apiFilePath), 500, "Api routes file not found! ({$apiFilePath})");

        return $apiFilePath;
    }
}
