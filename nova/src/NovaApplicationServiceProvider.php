<?php

namespace Laravel\Nova;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Exceptions\NovaExceptionHandler;
use Wdelfuego\Nova\DateTime\Fields\DateTime;

class NovaApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->gate();
        $this->routes();

        Nova::serving(function (ServingNova $event) {
            $this->authorization();
            $this->registerExceptionHandler();
            $this->resources();
            Nova::dashboards($this->dashboards());
            Nova::tools($this->tools());
        });

        DateTime::macro('displayWithDateTimeFormat', function(string|bool|null $format = null) {
            if( $format === false ) {
                $this->displayCallback = null;
                $this->fillCallback = null;

                return $this;
            }

            return $this->displayUsing(fn($d) => formatDateTime($d, $format))
                        ->fillUsing(function($request, $model, $attribute, $requestAttribute) {
                            $value = $request->$requestAttribute;
                            if( !is_null($value) ) {
                                $value = $model::castSingleAttribute($attribute, $value);
                                $value = $value->setTimezone(config('app.timezone'))->shiftTimezone('UTC')->toIso8601String();
                            }

                            $model->$attribute = $value;
                        });
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes();
    }

    /**
     * Configure the Nova authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        Nova::auth(function ($request) {
            return app()->environment('local') ||
                   Gate::check('viewNova', [Nova::user($request)]);
        });
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register Nova's custom exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        app()->bind(ExceptionHandler::class, NovaExceptionHandler::class);
    }

    /**
     * Register the application's Nova resources.
     *
     * @return void
     */
    protected function resources()
    {
        Nova::resourcesIn(app_path('Nova'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
