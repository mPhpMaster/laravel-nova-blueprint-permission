<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

if( !function_exists('currentLocale') ) {
    /**
     * return appLocale
     *
     * @param bool $full
     *
     * @return string
     */
    function currentLocale($full = false): string
    {
        if( $full )
            return (string) app()->getLocale();

        $locale = str_replace('_', '-', app()->getLocale());
        $locale = current(explode("-", $locale));

        return $locale ?: "";
    }
}

if( !function_exists('setCurrentLocale') ) {
    /**
     * @param \Closure|string|null $locale
     *
     * @return bool
     */
    function setCurrentLocale($locale = null): bool
    {
        try {
            $session = request()->session();
        } catch(Exception|Error $error) {
            try {
                $session = resolve('session');
                request()->setLaravelSession($session);
            } catch(Exception $exception) {
                $session = optional();
            }
        }
        $language = value($locale);
        $language ??= $session->get('language') ?: getDefaultLocale('en');

        if( $language && isLocaleAllowed($language) ) {
            if( currentLocale() !== $language ) {
                $session->put('language', $language);
                $session->save();

                app()->setLocale($language);
            }

            return true;
        }

        return false;
    }
}

if( !function_exists('currentUrl') ) {
    /**
     * Returns current url.
     *
     * @param string|null $key    return as array with key $key and value as url
     * @param bool        $encode use urlencode
     *
     * @return string|array
     */
    function currentUrl(?string $key = null, bool $encode = true)
    {
        $url = request()->url();
        $url = iif($encode, urlencode($url), $url);

        return is_null($key) ? $url : [ $key => $url ];
    }
}

if( !function_exists('currentUser') ) {
    /**
     * @param $default
     * @param $guard
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User|null
     */
    function currentUser($default = null, $guard = null): \Illuminate\Contracts\Auth\Authenticatable|User|null
    {
        return auth($guard ?? 'web')->user() ?? auth()->user() ?? $default;
    }
}

if( !function_exists('currentUserName') ) {
    /**
     * @param $default
     * @param $guard
     *
     * @return mixed
     */
    function currentUserName($default = null, $guard = null)
    {
        return optional(currentUser(null, $guard))->name ?? value($default);
    }
}

if( !function_exists('currentAuth') ) {
    /**
     * @param null        $default
     * @param string|null $guard
     *
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|\Illuminate\Contracts\Foundation\Application|mixed|null
     */
    function currentAuth($default = null, ?string $guard = '')
    {
        return (is_null($guard) ? auth() : auth($guard ?: 'web')) ?? $default;
    }
}

if( !function_exists('currentUserId') ) {
    /**
     * @param mixed $default
     *
     * @return int|string|null
     */
    function currentUserId($default = null, $guard = null)
    {
        return auth($guard ?? 'web')->id() ?? auth()->id() ?? $default;
    }
}

if( !function_exists('currentControllerFQN') ) {
    /**
     * @return string|null
     */
    function currentControllerFQN(): ?string
    {
        $route = Route::current();
        if( !$route ) return null;

        if( isset($route->controller) || method_exists($route, 'getController') ) {
            [ $controller, $method ] = Str::parseCallback($route->getActionName());

            return getClass($route->controller ?? $controller);
        }

        $action = $route->getAction();
        if( $action && isset($action[ 'controller' ]) ) {
            $currentAction = $action[ 'controller' ];
            [ $controller, $method ] = explode('@', $currentAction);

            return getClass($controller);
        }

        return null;
    }
}

if( !function_exists('currentControllerClass') ) {
    /**
     * @return string|null
     */
    function currentControllerClass(bool $base_name = true): ?string
    {
        $parseResult = fn($class) => ($class = $class ?: currentControllerFQN()) && $base_name ? class_basename($class) : $class;
        if( is_null($result = $route = currentRoute()) ) {
            return $parseResult($result);
        }

        if( !is_null($result = $route->controller ?? (method_exists($route, 'getControllerClass') ? $route->getControllerClass() : null)) ) {
            return $parseResult(getClass($result));
        }

        if( $action = $route->getAction() ) {
            if( isset($action[ 'controller' ]) ) {
                if( $controller = str_ireplace([ '@', '::', '->' ], '@', $action[ 'controller' ]) ) {
                    $controller = str_before($controller, '@');
                }

                $result = $controller ?: null;
            }
        }

        return $parseResult($result);
    }
}

if( !function_exists('currentAction') ) {
    /**
     * get current route
     *
     * @return string|null
     */
    function currentAction(): ?string
    {
        try {
            $array = explode('.', CurrentRoute()->getName());

            return @end($array) ?: currentActionName();
        } catch(Exception $exception) {
            return currentActionName();
        }
    }
}

if( !function_exists('currentModelViaControllerName') ) {
    /**
     * get current route
     *
     * @return string|null
     */
    function currentModelViaControllerName($controllerName = null): ?string
    {
        try {
            if( $controller = ($controllerName ?: currentControllerClass()) ) {
                $controller = str_before_last_count($controller, 'Controller');
                $controller = getModelClass($controller);
            }

            return $controller ?: currentModel();
        } catch(Exception $exception) {
            return currentModel();
        }
    }
}
