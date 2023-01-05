<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

/** @noinspection ForgottenDebugOutputInspection */

use App\Models\Abstracts\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Support\Facades\Route;
use libphonenumber\NumberParseException;

if( !function_exists('carbon') ) {
    /**
     * @return \Carbon\Carbon|\Illuminate\Foundation\Application|mixed
     */
    function carbon()
    {
        return app(\Carbon\Carbon::class);
    }
}

if( !function_exists('when') ) {
    /**
     * if $condition then call $whenTrue|null else call $whenFalse|null
     *
     * @param bool|mixed    $condition
     * @param callable|null $whenTrue
     * @param callable|null $whenFalse
     * @param mixed|null    $with
     *
     * @return mixed|null
     */
    function when($condition, callable $whenTrue = null, callable $whenFalse = null, $with = null)
    {
        $result = value($condition);

        return value($result ? $whenTrue : $whenFalse, $result, $with);
    }
}

// region: return
if( !function_exists('returnCallable') ) {
    /**
     * Determine if the given value is callable, but not a string.
     * **Source**: ---  {@link \Illuminate\Support\Collection Laravel Collection}
     *
     * @param mixed $value
     *
     * @return \Closure
     */
    function returnCallable($value): \Closure
    {
        if( !is_callable($value) ) {
            return returnClosure($value);
        }

        if( is_string($value) ) {
            return Closure::fromCallable($value);
        }

        return $value;
    }
}

if( !function_exists('returnClosure') ) {
    /**
     * Returns function that returns any arguments u sent;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnClosure(...$data)
    {
        $_data = head($data);
        if( func_num_args() > 1 ) {
            $_data = $data;
        } elseif( func_num_args() === 0 ) {
            $_data = returnNull();
        }

        return function() use ($_data) {
            return value($_data);
        };
    }
}

if( !function_exists('returnArray') ) {
    /**
     * Returns function that returns [];
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnArray(...$data)
    {
        return returnClosure($data);
    }
}

if( !function_exists('returnCollect') ) {
    /**
     * Returns function that returns Collection;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnCollect(...$data)
    {
        return function(...$args) use ($data) {
            return collect($data)->merge($args);
        };
    }
}

if( !function_exists('returnArgs') ) {
    /**
     * Returns function that returns func_get_args();
     *
     * @return \Closure
     */
    function returnArgs()
    {
        return function() {
            return func_get_args();
        };
    }
}

if( !function_exists('returnString') ) {
    /**
     * Returns function that returns ""
     *
     * @param string|null $text
     *
     * @return \Closure
     */
    function returnString(?string $text = "")
    {
        return returnClosure((string) $text);
    }
}

if( !function_exists('returnNull') ) {
    /**
     * Returns function that returns null;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnNull()
    {
        return function() {
            return null;
        };
    }
}

if( !function_exists('returnTrue') ) {
    /**
     * Returns function that returns true;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnTrue()
    {
        return returnClosure(true);
    }
}

if( !function_exists('returnFalse') ) {
    /**
     * Returns function that returns false;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnFalse()
    {
        return returnClosure(false);
    }
}
// endregion: return

#region IS
if( !function_exists('firstSet') ) {
    /**
     * @param mixed ...$var
     *
     * @return mixed|null
     */
    function firstSet(...$var)
    {
        foreach( $var as $_var )
            if( isset($_var) )
                return $_var;

        return null;
    }
}

if( !function_exists('getAny') ) {
    /**
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function getAny(...$vars)
    {
        foreach( $vars as $_var ) {
            if( $_var ) {
                return $_var;
            }
        }

        return null;
    }
}

if( !function_exists('test') ) {
    /**
     * Apply `value` function to each argument. when value returns something true ? return it.
     *
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function test(...$vars)
    {
        foreach( $vars as $_var )
            if( $_var = value($_var) ) {
                return $_var;
            }

        return null;
    }
}

if( !function_exists('iif') ) {
    /**
     * Test Condition and return one of two parameters
     *
     * @param mixed $var   Condition
     * @param mixed $true  Return this if Condition == true
     * @param mixed $false Return this when Condition fail
     *
     * @return mixed
     */
    function iif($var, $true = null, $false = null)
    {
        return value(value($var) ? $true : $false);
    }
}
#endregion

#region HAS
if( !function_exists('hasTrait') ) {
    /**
     * Check if given class has trait.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $traitName <p>
     *                          Trait name to check
     *                          </p>
     *
     * @return bool
     */
    function hasTrait($class, $traitName)
    {
        try {
            $traitName = str_contains($traitName, "\\") ? class_basename($traitName) : $traitName;

            $hasTraitRC = new ReflectionClass($class);
            $hasTrait = collect($hasTraitRC->getTraitNames())->map(function($name) use ($traitName) {
                    $name = str_contains($name, "\\") ? class_basename($name) : $name;

                    return $name == $traitName;
                })->filter()->count() > 0;
        } catch(ReflectionException $exception) {
            $hasTrait = false;
        } catch(Exception $exception) {
            // dd($exception->getMessage());
            $hasTrait = false;
        }

        return $hasTrait;
    }
}

if( !function_exists('hasKey') ) {
    /**
     * Check if given array has key if has key call $callable.
     *
     * @param array        $array
     * @param string       $key
     * @param Closure|null $callable
     *
     * @return bool|mixed
     */
    function hasKey($array, $key, Closure $callable = null)
    {
        try {
            $has = array_key_exists($key, $array);
            if( $callable && is_callable($callable) ) {
                return $callable->call($array, $array);
            }

            return $has === true;
        } catch(Exception $exception) {
            // d($exception->getMessage());
        }

        return false;
    }
}

if( !function_exists('hasScope') ) {
    /**
     * Check if given class has the given scope name.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $scopeName <p>
     *                          Scope name to check
     *                          </p>
     *
     * @return bool
     */
    function hasScope($class, $scopeName)
    {
        try {
            $hasScopeRC = new ReflectionClass($class);
            $scopeName = strtolower(studly_case($scopeName));
            starts_with($scopeName, "scope") && ($scopeName = substr($scopeName, strlen("scope")));

            $hasScope = collect($hasScopeRC->getMethods())->map(function($c) use ($scopeName) {
                    /**
                     * @var $c ReflectionMethod
                     */
                    $name = strtolower(studly_case($c->getName()));
                    $name = starts_with($name, "scope") ? substr($name, strlen("scope")) : false;

                    return $name == $scopeName;
                })->filter()->count() > 0;
        } catch(ReflectionException $exception) {
            $hasScope = false;
        } catch(Exception $exception) {
            $hasScope = false;
        }

        return ! !$hasScope;
    }
}

if( !function_exists('hasConst') ) {
    /**
     * Check if given class has the given const.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $const     <p>
     *                          Const name to check
     *                          </p>
     *
     * @return bool
     */
    function hasConst($class, $const): bool
    {
        $hasConst = false;
        try {
            if( is_object($class) || is_string($class) ) {
                $reflect = new ReflectionClass($class);
                $hasConst = array_key_exists($const, $reflect->getConstants());
            }
        } catch(ReflectionException $exception) {
            $hasConst = false;
        } catch(Exception $exception) {
            $hasConst = false;
        }

        return (bool) $hasConst;
    }
}

if( !function_exists('getConst') ) {
    /**
     * Returns const value if exists, otherwise returns $default.
     *
     * @param string|array $const   <p>
     *                              Const name to check
     *                              </p>
     * @param mixed|null   $default <p>
     *                              Value to return when const not found
     *                              </p>
     *
     * @return mixed
     */
    function getConst($const, $default = null)
    {
        return defined($const = is_array($const) ? implode("::", $const) : $const) ? constant($const) : $default;
    }
}
#endregion

#region GET
if( !function_exists('str_prefix') ) {
    /**
     * Add a prefix to string but only if string2 is not empty.
     *
     * @param string      $string  string to prefix
     * @param string      $prefix  prefix
     * @param string|null $string2 string2 to prefix the return
     *
     * @return string|null
     */
    function str_prefix($string, $prefix, $string2 = null)
    {
        $newString = rtrim(is_null($string2) ? '' : $string2, $prefix) .
            $prefix .
            ltrim($string, $prefix);

        return ltrim($newString, $prefix);
    }
}

if( !function_exists('str_suffix') ) {
    /**
     * Add a suffix to string but only if string2 is not empty.
     *
     * @param string      $string  string to suffix
     * @param string      $suffix  suffix
     * @param string|null $string2 string2 to suffix the return
     *
     * @return string|null
     */
    function str_suffix($string, $suffix, $string2 = null)
    {
        $newString = ltrim($string, $suffix) . $suffix . rtrim(is_null($string2) ? '' : $string2, $suffix);

        return trim($newString, $suffix);
    }
}

if( !function_exists('str_words_limit') ) {
    /**
     * Limit string words.
     *
     * @param string      $string string to limit
     * @param int         $limit  word limit
     * @param string|null $suffix suffix the string
     *
     * @return string
     */
    function str_words_limit($string, $limit, $suffix = '...')
    {
        $start = 0;
        $stripped_string = strip_tags($string); // if there are HTML or PHP tags
        $string_array = explode(' ', $stripped_string);
        $truncated_array = array_splice($string_array, $start, $limit);

        $lastWord = end($truncated_array);
        $return = substr($string, 0, stripos($string, $lastWord) + strlen($lastWord)) . ' ' . $suffix;

        $m = [];
        if( preg_match_all('#<(\w+).+?#is', $return, $m) ) {
            $m = is_array($m) && is_array($m[ 1 ]) ? array_reverse($m[ 1 ]) : [];
            foreach( $m as $HTMLTAG ) {
                $return .= "</{$HTMLTAG}>";
            }
        }

        return $return;
    }
}

if( !function_exists('getTrans') ) {
    /**
     * Translate the given message or return default.
     *
     * @param string|null $key
     * @param array       $replace
     * @param string|null $locale
     *
     * @return string|array|null
     */
    function getTrans($key = null, $default = null, $replace = [], $locale = null)
    {
        $key = value($key);
        $return = __($key, $replace, $locale);

        return $return === $key ? value($default) : $return;
    }
}

#endregion

if( !function_exists('getRecommendedSizeTrans') ) {
    /**
     * @param        $n1     Number
     * @param        $n2     Number
     * @param string $prepend
     * @param null   $locale 'ar' or 'en'
     * @param string $size_separator
     * @param string $prefix
     * @param string $suffix
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    function getRecommendedSizeTrans($n1, $n2, $prepend = '', $locale = null, $size_separator = 'x', $prefix = '(', $suffix = ')')
    {
        return getCustomRecommendedSizeTrans('common.recommended_size', $n1, $n2, $prepend, $locale, $size_separator, $prefix, $suffix);
    }
}

if( !function_exists('getCustomRecommendedSizeTrans') ) {
    /**
     * @param string|null $title
     * @param             $n1     Number
     * @param             $n2     Number
     * @param string      $prepend
     * @param null        $locale 'ar' or 'en'
     * @param string      $size_separator
     * @param string      $prefix
     * @param string      $suffix
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    function getCustomRecommendedSizeTrans(?string $title, $n1, $n2, $prepend = '', $locale = null, $size_separator = 'x', $prefix = '(', $suffix = ')')
    {
        $prepend = $prepend ? "{$prepend} " : "";

        return $prepend .
            $prefix .
            __($title ?? 'common.recommended_size', [
                'n1' => $n1,
                'separator' => $size_separator,
                'n2' => $n2,
            ], $locale ?: currentLocale()) .
            $suffix;
    }
}

if( !function_exists('getDatePeriodUntil') ) {
    /**
     * @param \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon|\Closure|null $from_date
     * @param \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon|\Closure|null $to_date
     * @param string|\Closure|null                                                                   $format
     * @param string|\Closure|null                                                                   $untilUnit
     *
     * @return \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon|\Generator|\Iterator
     */
    function getDatePeriodUntil($from_date = null, $to_date = null, $format = 'Y-m-d', $untilUnit = 'months')
    {
        /** @value \Carbon\Carbon $from_date */
        $from_date = value($from_date ?? getDefaultFromDate());
        /** @value \Carbon\Carbon $to_date */
        $to_date = value($to_date ?? getDefaultToDate());
        /** @value string|null $untilUnit */
        $untilUnit = ($untilUnit = value($untilUnit)) && is_string($untilUnit)
            ? str_finish($untilUnit, 'Until') : null;
        /** @value string|null $format */
        $format = value($format);

        if( $untilUnit && is_string($untilUnit) ) {
            $result = $from_date->$untilUnit($to_date);
        }

        if( !is_null($format) ) {
            /** @var \Carbon\Carbon $v */
            $result = $result->map(fn($v) => $v->format((string) $format));
        }

        return $result;
    }
}

if( !function_exists('getDatePeriodUntilAsArray') ) {
    /**
     * @param \Carbon\Carbon|\Closure|null $from_date
     * @param \Carbon\Carbon|\Closure|null $to_date
     * @param string|\Closure|null         $format
     * @param string|\Closure|null         $untilUnit
     *
     * @return array
     */
    function getDatePeriodUntilAsArray($from_date = null, $to_date = null, $format = 'Y-m-d', $untilUnit = 'months')
    {
        return iterator_to_array(getDatePeriodUntil(...func_get_args()));
    }
}

if( !function_exists('getModelRelationAttribute') ) {
    /**
     * @param \Illuminate\Database\Eloquent\Concerns\HasRelationships|\App\Models\Model $model
     * @param string|\Closure                                                           $relation_name
     * @param string|\Closure                                                           $attribute
     * @param mixed|null                                                                $default
     *
     * @return mixed
     */
    function getModelRelationAttribute(HasRelationships|Model $model, $relation_name, $attribute, $default = null)
    {
        $relation_name = value($relation_name);
        $attribute = value($attribute);
        if( $model->relationLoaded($relation_name) ) {
            if( $relation = $model->$relation_name ) {
                $default = $relation->$attribute;
            }
        } else {
            $default = $model->$relation_name()->value($attribute);
        }

        return value($default);
    }
}

if( !function_exists('hasTranslationTraits') ) {
    /**
     * @param \App\Models\Model|string $model
     *
     * @return bool
     */
    function hasTranslationTraits(Model|string $model): bool
    {
        return
            hasTrait($model, \Spatie\Translatable\HasTranslations::class) ||
            hasTrait($model, \App\Traits\HasTranslations::class);
    }
}

if( !function_exists('uniqueRuleValidator') ) {
    /**
     * @param \App\Nova\Resource     $resource
     * @param \App\Models\Model|null $model
     *
     * @return \Closure
     */
    function uniqueRuleValidator(\App\Nova\Resource $resource, ?Model $model): Closure
    {
        $instance = $model ?? $resource::newModel();

        return function($attribute, $value, $fail) use ($instance) {
            $attributeLocale = null;
            if( str_contains($attribute, '.') ) {
                [ $attribute, $attributeLocale ] = explode(".", $attribute);
            }
            $failed = false;
            if( hasTranslationTraits($instance) && $instance->isTranslatableAttribute($attribute) ) {
                if( $instance->newQuery()->where("{$attribute}->$attributeLocale", $value)->when($instance->id, fn($q) => $q->whereKeyNot($instance->id))->exists() ) {
                    $failed = true;
                }
            } else {
                if( $instance->newQuery()->where($attribute, $value)->when($instance->id, fn($q) => $q->whereKeyNot($instance->id))->exists() ) {
                    $failed = true;
                }
            }

            if( $failed ) {
                $attributeLocaleName = $attributeLocale ? data_get(array_flip(getLocales(true)), $attributeLocale) : null;
                $attribute = $instance::trans($attribute) . ($attributeLocaleName ? " (" . __($attributeLocaleName) . ")" : "");
                $fail(__('validation.unique', compact('attribute')));
            }
        };
    }
}

if( !function_exists('isCommandExists') ) {
    /**
     * @param string $command
     *
     * @return bool
     */
    function isCommandExists(string $command): bool
    {
        return trim($command) && collect(\Artisan::all())
                ->filter(fn($c) => $c && $c->isHidden() !== true)
                ->keys()
                ->filter()
                ->contains($command);
    }
}

if( !function_exists('getSql') ) {
    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return string
     */
    function getSql(Builder $builder): string
    {
        return $sql = sprintf(str_ireplace('?', '%s', $builder->toSql()), ...$builder->getBindings());
    }
}

if( !function_exists('getPhoneCountryCode') ) {
    /**
     * @param string $international_number
     *
     * @return string|null
     */
    function getPhoneCountryCode(string $international_number): ?string
    {
        return function_exists('phone') ?
            phone(fix_phone_prefix($international_number))->getCountry() :
            null;
    }
}

if( !function_exists('getPhoneCountryName') ) {
    /**
     * @param string      $international_number
     * @param string|null $locale
     *
     * @return string|null
     */
    function getPhoneCountryName(string $international_number, ?string $locale = null): ?string
    {
        $phone = phone(fix_phone_prefix($international_number))->getPhoneNumberInstance();
        $locale = $locale ?: currentLocale();

        return \libphonenumber\geocoding\PhoneNumberOfflineGeocoder::getInstance()
                                                                   ->getDescriptionForNumber($phone, $locale);
    }
}

if( !function_exists('getPhoneCountryIcon') ) {
    /**
     * @param string $international_number
     *
     * @return string|null
     */
    function getPhoneCountryIcon(string $international_number, bool $png = false): ?string
    {
        $type = $png ? "png" : "svg";
        if( $icon = file_get_contents("https://countryflagsapi.com/{$type}/" . getPhoneCountryCode($international_number)) ) {
            return $icon;
        }

        return null;
    }
}

// region: locale
if( !function_exists('isLocaleAllowed') ) {
    /**
     * @param string|\Closure $locale
     *
     * @return bool
     */
    function isLocaleAllowed($locale): bool
    {
        return array_key_exists($locale, array_flip(getLocales(true)));
    }
}

if( !function_exists('getLocales') ) {
    /**
     * @return array
     */
    function getLocales(bool $withNames = false): array
    {
        $locales = config('app.locales', config('nova.locales', []));

        return $withNames ? array_flip($locales) : array_keys($locales);
    }
}

if( !function_exists('getDefaultLocale') ) {
    /**
     * @param string|\Closure $default
     *
     * @return string|null
     */
    function getDefaultLocale($default = 'en'): string|null
    {
        $default = value($default);

        return config('app.locale', config('app.fallback_locale', $default)) ?: $default;
    }
}
// endregion: locale

if( !function_exists('apiResource') ) {
    /**
     * Route an API resource to a controller.
     *
     * @param string $name
     * @param string $controller
     * @param array  $options [except=>,only=>]
     *
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    function apiResource($name, $controller, array $options = [])
    {
        $only = [ 'index', 'show', 'store', 'update', 'destroy', 'forceDestroy', 'force_destroy', 'forceDelete', 'force_delete', 'restore' ];

        if( isset($options[ 'except' ]) ) {
            $options[ 'except' ] = array_map(fn($value) => ends_with(snake_case($value), "_delete") ? str_ireplace("_delete", "_destroy", snake_case($value)) : $value, (array) $options[ 'except' ]);
            if( in_array('force_destroy', $options[ 'except' ]) ) {
                $options[ 'except' ][] = 'force_delete';
            }

            $only = array_diff($only, $options[ 'except' ]);
            $only = array_diff($only, array_map('snake_case', $options[ 'except' ]));
            $only = array_diff($only, array_map('camel_case', $options[ 'except' ]));
        }
        $only = array_combine($only, $only);

        $sName = str_singular($name);
        if( $only[ 'forceDestroy' ] ?? $only[ 'force_destroy' ] ?? false ) {
            Route::delete("{$name}/{{$sName}}/force", [ $controller, 'forceDestroy' ])->name("{$name}.force_delete")->withTrashed();
        }
        if( $only[ 'restore' ] ?? false ) {
            Route::post("{$name}/{{$sName}}/restore", [ $controller, 'restore' ])->name("{$name}.restore")->withTrashed();
        }

        return Route::resource(
            $name,
            $controller,
            array_merge([
                            'only' => array_keys($only),
                        ], $options)
        );
    }
}

if( !function_exists('apiResources') ) {
    /**
     * Register an array of API resource controllers.
     *
     * @param array $resources
     * @param array $options
     *
     * @return void
     */
    function apiResources(array $resources, array $options = [])
    {
        foreach( $resources as $name => $controller ) {
            apiResource($name, $controller, $options);
        }
    }
}

if( !function_exists('unauthenticated') ) {
    /**
     * @param string $message
     * @param int    $status
     *
     * @return \Illuminate\Auth\AuthenticationException
     */
    function unauthenticated($message = null, $status = 422)
    {
        $message ??= __("auth.unauthenticated");

        return new \Illuminate\Auth\AuthenticationException($message);
//        return new \Exception($message, $status);
    }
}

if( !function_exists('throwUnauthenticated') ) {
    /**
     * @param string $message
     * @param int    $status
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    function throwUnauthenticated($message = null, $status = 401)
    {
        $message ??= __("auth.unauthenticated");
        throw unauthenticated($message, $status);
    }
}

if( !function_exists('logout') ) {
    /**
     * @param $guard
     *
     * @return callable|mixed|null
     */
    function logout($guard = null)
    {
        return when(currentAuth(auth(), $guard), fn($a) => $a->logout());
    }
}

if( !function_exists('login') ) {
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param                                            $remember
     * @param                                            $guard
     *
     * @return callable|mixed|null
     */
    function login(Authenticatable $user, $remember = false, $guard = null)
    {
        return when(currentAuth(auth(), $guard), fn($a) => $a->login($user, $remember));
    }
}

if( !function_exists('isCurrentAction') ) {
    /**
     * get current route
     *
     * @return bool
     */
    function isCurrentAction($mode): bool
    {
        return strtolower(trim($mode)) == strtolower(trim(currentAction()));
    }
}

