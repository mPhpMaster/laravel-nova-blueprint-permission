<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\URL;


use App\Interfaces\IImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if(!function_exists('generateValidationRequiredMessages')) {
	function generateValidationRequiredMessages(array $validations, callable|Closure|null $trans = null): array
	{
		$trans ??= __(...);
		$messages = [];
		foreach($validations as $field => $rules) {
			if(array_contains($rules, 'required')) {
				$messages["$field.required"] = __('validation.required', [
					'attribute' => $trans($field),
				]);
			}
		}

		return $messages;
	}
}

if(!function_exists('array_contains')) {
	function array_contains(array $array, string|Closure|Stringable $contains)
	{
		$contain = (string) value($contains);
		foreach($array as $item) {
			if(mb_strpos($item, $contain) !== false) {
				return true;
			}
		}

		return false;
	}
}

if(!function_exists('array_only_except')) {
	/**
	 * Get two arrays, one has the second argument, and another one without it
	 *
	 * @param array        $array
	 * @param array|string $keys
	 *
	 * @return array
	 */
	function array_only_except($array, $keys): array
	{
		return [
			array_only($array, $keys),
			array_except($array, $keys),
		];
	}
}

if(!function_exists('array_except_only')) {
	/**
	 * Get two arrays, one without the second argument, and another one with it
	 *
	 * @param array        $array
	 * @param array|string $keys
	 *
	 * @return array
	 */
	function array_except_only($array, $keys): array
	{
		return [
			array_except($array, $keys),
			array_only($array, $keys),
		];
	}
}

if(!function_exists('getSql')) {
	/**
	 * @param \Illuminate\Database\Eloquent\Builder $builder
	 *
	 * @return string
	 */
	function getSql(Builder|Relation|\Illuminate\Contracts\Database\Query\Builder $builder, bool $parse = false): string
	{
		$sql = sprintf(str_ireplace('?', "'%s'", $builder->toSql()), ...$builder->getBindings());

		return !$parse ? $sql : replaceAll([
			' or '    => "\n\t\tor ",
			' and '   => "\n\t\tand ",
			' where ' => "\n\twhere ",
		], $sql);
	}
}

if(!function_exists('guessModelsViaController')) {
	/**
	 * @param string         $controller
	 * @param \Closure|mixed $default
	 *
	 * @return array
	 */
	function guessModelsViaController(string $controller, mixed $default = null): array
	{
		$controller = ltrim($controller, '\\/');
		$controller = str_replace('/', '\\', $controller);
		if(ends_with($controller, 'Controller')) {
			$controller = str_before_last_count(class_basename($controller), 'Controller');
		}
		$controller = !class_exists($controller) && class_exists(studly_case($controller)) ? studly_case($controller) : $controller;

		if(!class_exists($controller)) {
			if(class_exists($model = "\\App\\Models\\{$controller}")) {
				$controller = $model;
			} else {
				$models = collect();
				foreach(explode('-', snake_case($controller, '-')) as $model) {
					if(!$model) {
						continue;
					}

					$model = studly_case(str_singular($model));
					if(count($_models = array_wrap(guessModelsViaController($model)))) {
						$models->push(...$_models);
					}
				}

				$controller = $models->filter()->unique()->all();
			}
		}

		$controller = array_filter(array_wrap($controller), 'isModel');

		return count($controller) ? $controller : array_wrap(value($default));
	}
}

if(!function_exists('getMethodName')) {
	/**
	 * Returns method name by given Route->uses
	 *
	 * @param string $method
	 *
	 * @return string
	 */
	function getMethodName(string $method): string
	{
		if(empty($method)) return '';

		if(stripos($method, '::') !== false)
			$method = collect(explode('::', $method))->last();

		if(stripos($method, '@') !== false)
			$method = collect(explode('@', $method))->last();

		return $method;
	}
}

if(!function_exists('getRealClassName')) {
	/**
	 * Returns the real class name.
	 *
	 * @param string|object $class <p> The tested class. This parameter may be omitted when inside a class. </p>
	 *
	 * @return string|false <p> The name of the class of which <i>`class`</i> is an instance.</p>
	 * <p>
	 *      Returns <i>`false`</i> if <i>`class`</i> is not an <i>`class`</i>.
	 *      If <i>`class`</i> is omitted when inside a class, the name of that class is returned.
	 * </p>
	 */
	function getRealClassName($class): bool|string
	{
		if(is_object($class)) {
			$class = get_class($class);
		}
		throw_if(!class_exists($class), new Exception("Class `{$class}` not exists!"));

		try {
			$_class = eval(sprintf('return new class extends %s {  };', $class));
		} catch(Exception $exception) {
			dd(
				$exception->getMessage(),
				$exception
			);
		}

		if($_class && is_object($_class)) {
			return get_parent_class($_class);
		}

		return false;
	}
}

if(!function_exists('getClass')) {
	/**
	 * Returns the name of the class of an object
	 *
	 * @param object|Model|string $object |string [optional] <p> The tested object. This parameter may be omitted when inside a class. </p>
	 *
	 * @return string|false <p> The name of the class of which <i>`object`</i> is an instance.</p>
	 * <p>
	 *      Returns <i>`false`</i> if <i>`object`</i> is not an <i>`object`</i>.
	 *      If <i>`object`</i> is omitted when inside a class, the name of that class is returned.
	 * </p>
	 */
	function getClass($object): string|false
	{
		if(is_object($object)) {
			return get_class((object) $object);
		}

		return $object && is_string($object) && class_exists($object) ? $object : false;
	}
}

if(!function_exists('isClosure')) {
	/**
	 * Check if the given var is Closure.
	 *
	 * @param mixed|null $closure
	 *
	 * @return bool
	 */
	function isClosure($closure): bool
	{
		return $closure instanceof Closure;
	}
}

if(!function_exists('isRunningInConsole')) {
	/**
	 * @return bool
	 */
	function isRunningInConsole()
	{
		static $runningInConsole = null;

		if(isset($_ENV['APP_RUNNING_IN_CONSOLE']) || isset($_SERVER['APP_RUNNING_IN_CONSOLE'])) {
			return ($runningInConsole = $_ENV['APP_RUNNING_IN_CONSOLE']) ||
				($runningInConsole = $_SERVER['APP_RUNNING_IN_CONSOLE']) === 'true';
		}

		return $runningInConsole = $runningInConsole ?: (
			\Illuminate\Support\Env::get('APP_RUNNING_IN_CONSOLE') ??
			(\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg' || in_array(php_sapi_name(), [ 'cli', 'phpdb' ]))
		);
	}
}

if(!function_exists('whenRunningInConsole')) {
	/**
	 * return first argument if user is logged in otherwise return second argument.
	 *
	 * @return mixed
	 */
	function whenRunningInConsole(callable $when_true = null, callable $when_false = null)
	{
		return is_callable($value = $isRunningInConsole = isRunningInConsole() ? $when_true : $when_false) ?
			call_user_func_array($value, [ $isRunningInConsole, currentUser() ]) :
			$value;
	}
}

if(!function_exists('toBoolValue')) {
	/**
	 * Returns value as boolean
	 *
	 * @param $var
	 *
	 * @return bool
	 */
	function toBoolValue($var): bool
	{
		if(is_bool($var)) return boolval($var);

		!is_bool($var) && ($var = strtolower(trim($var)));
		!is_bool($var) && ($var = $var === 'false' ? false : $var);
		!is_bool($var) && ($var = $var === 'true' ? true : $var);
		!is_bool($var) && ($var = $var === '1' ? true : $var);
		!is_bool($var) && ($var = $var === '0' ? false : $var);

		return boolval($var);
	}
}

if(!function_exists('getDefaultDateTimeFormat')) {
	/**
	 * @return string
	 */
	function getDefaultDateTimeFormat(): string
	{
		return (string) config('app.datetime_format');
	}
}

if(!function_exists('getDefaultTimeFormat')) {
	/**
	 * @return string
	 */
	function getDefaultTimeFormat(): string
	{
		return (string) config('app.time_format');
	}
}

if(!function_exists('getDefaultDateFormat')) {
	/**
	 * @return string
	 */
	function getDefaultDateFormat(): string
	{
		return (string) config('app.date_format');
	}
}

if(!function_exists('formatDateTime')) {
	/**
	 * @param \Illuminate\Support\Carbon|\DateTimeInterface|mixed $d
	 * @param string|null                                         $format
	 *
	 * @return mixed|string
	 */
	function formatDateTime($d, ?string $format = null)
	{
		$format = $format ?? getDefaultDateTimeFormat();

		$isCarbon = ($d instanceof \Illuminate\Support\Carbon);
		$isDateTime = ($d instanceof \DateTimeInterface);

		if($isCarbon || $isDateTime) {
			$d = config('app.timezone') ? $d->setTimezone(new \DateTimeZone(config('app.timezone'))) : $d;

			$d->locale('en');

			if($isCarbon) {
				return $d->translatedFormat($format);
			}

			if($isDateTime) {
				return $d->format($format);
			}
		}

		return '';
	}
}

if(!function_exists('formatDateTimeWithDiff')) {
	/**
	 * @param \Illuminate\Support\Carbon|\DateTimeInterface|mixed $d
	 * @param string|null                                         $format
	 *
	 * @return mixed|string
	 */
	function formatDateTimeWithDiff($d, ?string $format = null)
	{
		$format = $format ?? getDefaultDateTimeFormat();

		$isCarbon = ($d instanceof \Illuminate\Support\Carbon);
		$isDateTime = ($d instanceof \DateTimeInterface);

		if($isCarbon || $isDateTime) {
			$d = config('app.timezone') ? $d->setTimezone('UTC') : $d;

			if($isCarbon) {
				return $d->translatedFormat($format).' ('.$d->longRelativeToOtherDiffForHumans().')';
			}

			if($isDateTime) {
				return $d->format($format).' ('.$d->longRelativeToOtherDiffForHumans().')';
			}
		}

		return '';
	}
}

function getRules($model, $field, $default = '')
{
	$default = value($default);
	if($rules = $model::$rules ?? $model::rules ?? []) {
		if($field) {
			if($ruleField = $rules[ $field ] ?? null) {
				return explode('|', $ruleField) ?? $default;
			}

			return $default;
		}

		return $rules ?? $default;
	}

	return explode('|', IImage::rules);
	// return explode('|', $model::$rules[ $field ] ?? '');
}

/*
function isSupport(): bool
{
    return auth()->user() && in_array(auth()->user()->user_type, [ 'support', 'developer' ]);
}

function isAdmin(): bool
{
    return auth()->user() && auth()->user()->user_type == 'admin';
}*/
function format_phone($phone)
{
	// remove empty spaces
	$phone = str_replace(' ', '', $phone);
	// remove country code & replace it with 0
	$phone = preg_replace('/^\+966/', '0', $phone);
	$phone = preg_replace('/^00966/', '0', $phone);
	$phone = preg_replace('/^966/', '0', $phone);

	// convert_to_en_numbers
	$phone = convert_to_en_numbers($phone);

	return $phone;
}

function format_and_validate_phone($phone)
{
	$phone = format_phone($phone);

	// validate number
	$res = preg_match('/^05[0-9]{8}+$/', $phone);

	if(!$res) {
		throw new Exception(__('api.valid phone number format'), 1);
	}

	return $phone;
}

function imageResize($imageData, ?string $newPath = 'images')
{
	$image_normal = Image::make($imageData);
	$image_thumbnail = Image::make($imageData);
	$extension = explode('/', $image_normal->mime())[1];
	$fileName = $newPath.'/'.now()->timestamp.'_'.uniqid();

	if($newPath) {
		$pathinfo = pathinfo($newPath);
		if(isset($pathinfo['extension'])) {
			$extension = $pathinfo['extension'];
		}
		if($pathinfo['dirname'] === '.') {
			$pathinfo['dirname'] = $newPath;
			$pathinfo['filename'] = basename($fileName);
		}
		$fileName = "{$pathinfo['dirname']}/{$pathinfo['filename']}";
	}

	if($image_normal->width() > 750) {
		$image_normal = $image_normal->resize(750, null, function($constraint) {
			$constraint->aspectRatio();
		});
	}
	if($image_thumbnail->width() > 320) {
		$image_thumbnail = $image_thumbnail->resize(320, null, function($constraint) {
			$constraint->aspectRatio();
		});
	}

	$image_normal = $image_normal->encode($extension, 85)
		->stream();
	$image_thumbnail = $image_thumbnail->encode($extension, 75)
		->stream();

	Storage::put("public/{$fileName}.{$extension}", $image_normal->__toString());
	Storage::put("public/{$fileName}_thumbnail.{$extension}", $image_thumbnail->__toString());

	return $fileName.'.'.$extension;
}

function imageThumbnail($image = null)
{
	if(!$image) return null;
	$exs = pathinfo($image, PATHINFO_EXTENSION);
	$thumbnail = "_thumbnail.{$exs}";

	return str_replace(".{$exs}", $thumbnail, $image);
}

function file_url($path)
{
	if(!$path) return asset('app/images/not-available.png');
	if(\Illuminate\Support\Str::startsWith(strtolower($path), 'http')) {
		return $path;
	}

	return asset($path);
	/* $prefix = starts_with($path, '/storage/') || starts_with($path, 'storage/') ? '' : 'storage/';
	 $pathWithPrefix = $prefix . $path;
	 $pathWithPrefix = starts_with($pathWithPrefix, '/') ? $pathWithPrefix : "/{$pathWithPrefix}";
	 $bucket = "https://s3.amazonaws.com/riyaadi";
	 return $bucket . $pathWithPrefix;*/
}

function s_public_path($path = null)
{
	return storage_path('app/public/'.$path);
}

function getTranslatedField($model, $field, $value)
{
	if(app()->getLocale() == 'en' && $model->{$field.'_en'} !== null) {
		return $model->{$field.'_en'};
	} else {
		return $value;
	}
}

/**
 * return column_{appLocale}
 */
if(!function_exists('columnLocalize')) {
	/**
	 * Localize column name.
	 *
	 * @param string      $columnName Column name
	 * @param string|null $locale     Locale name, Null = current locale name
	 *
	 * @return string
	 */
	function columnLocalize($columnName = 'name', $locale = null)
	{
		return ltrim($columnName, '_').'_'.($locale ?: currentLocale());
	}
}

/**
 * Check if the current request is 'create new model' or 'index'
 * **use it in nova requests**
 *
 * @return bool
 */
function isNovaRequestCreateOrListMode(): bool
{
	return request()->resourceId === null;
}

/**
 * @param string        $class
 * @param callable|null $callback
 *
 * @return string
 */
function getClassUriKey(string $class, callable $callback = null): string
{
	$class = Str::plural(Str::kebab(class_basename($class)));

	return is_callable($callback) ? value($callback, $class) : $class;
}

/**
 * @param array|\Illuminate\Support\Collection $aArray1
 * @param array|\Illuminate\Support\Collection $aArray2
 *
 * @return array The differences
 */
function array_recursive_diff($aArray1, $aArray2, callable $callback = null): array
{
	$aArray1 = $aArray1 instanceof \Illuminate\Support\Collection ? $aArray1->toArray() : $aArray1;
	$aArray2 = $aArray2 instanceof \Illuminate\Support\Collection ? $aArray2->toArray() : $aArray2;
	$callback = $callback ?: fn(...$a) => $a;
	$aReturn = [];

	foreach($aArray1 as $mKey => $mValue) {
		if(array_key_exists($mKey, $aArray2)) {
			if(is_array($mValue)) {
				$aRecursiveDiff = array_recursive_diff($mValue, $aArray2[ $mKey ]);
				if(count($aRecursiveDiff)) {
					$aReturn[ $mKey ] = $aRecursiveDiff;
				}
			} else {
				if($mValue != $aArray2[ $mKey ]) {
					$aReturn[ $mKey ] = $callback([
						'expect' => $mValue,
						'actual' => $aArray2[ $mKey ],
					]);
				}
			}
		} else {
			$aReturn[ $mKey ] = $mValue;
		}
	}

	return $aReturn;
}

if(!function_exists('currencyNumberFormat')) {
	/**
	 * @param int|float|string|\Closure $value
	 * @param array                     $options [currency = null, locale = null, digits = 2]
	 *
	 * @return string
	 */
	function currencyNumberFormat(
		$value,
		array $options = [
			'currency' => null,
			'locale'   => 'en',
			'digits'   => 0,
		],
		$getterMethod = 'last',
		$prefix = null,
		$suffix = null,
		$format = '%s'
	): string
	{
		$value = $getterMethod(explode(' ', trim(currencyFormat($value, $options))));
		if(data_get($options, 'digits') === 0) {
			$value = R($value);
		}
		$prefix = ($prefix ?: '');
		$suffix = ($suffix ?: '');
		$value = ($prefix ? "{$prefix} " : '').trim($value).($suffix ? " {$suffix}" : '');
		$value = sprintf($format, $value);

		return $value;
	}
}

if(!function_exists('numberFormat')) {
	/**
	 * @param int|float|string|\Closure $value
	 *
	 * @return string
	 */
	function numberFormat($value): string
	{
		return trim(number_format($value, str_contains(trim($value), '.') ? 2 : 0));
	}
}











if( !function_exists('getRequestedPage') ) {
    /**
     * @param int                           $default
     * @param \Illuminate\Http\Request|null $request
     *
     * @return int|null
     */
    function getRequestedPage(int $default = 0, \Illuminate\Http\Request &$request = null)
    {
        $request ??= request();
        if( !$request->has('page') ) {
            return $default ?? null;
        }

        $page = $request->get('page', $default);

        return strtolower($page) === 'all' ? 0 : $page;
    }
}

if( !function_exists('getRequestedPageCount') ) {
    /**
     * @param int                           $default
     * @param \Illuminate\Http\Request|null $request
     * @param string                        $key
     *
     * @return int|null
     */
    function getRequestedPageCount(int $default = null, \Illuminate\Http\Request &$request = null, $key = "itemsPerPage")
    {
        $request ??= request();
        $default ??= config('app.per_page', null);
        if( !$request->has($key) ) {
            return $default;
        }

        $itemsPerPage = $request->get($key, $default);

        return strtolower($itemsPerPage) === 'all' ? -1 : $itemsPerPage;
    }
}

if( !function_exists('currentRoute') ) {
    /**
     * Returns current route
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Routing\Route|mixed
     */
    function currentRoute()
    {
        $route = Route::current();
        $route = $route ?: app(Route::class);

        return $route;
    }
}

if( !function_exists('currentActionName') ) {
    /**
     * @param null $action
     *
     * @return null
     */
    function currentActionName($action = null): ?string
    {
        try {
            $action = $action ?:
                Route::current()->getActionName() ?:
                    currentRoute()->getActionMethod() ?:
                        Route::currentRouteAction() ?:
                            Route::current()->getName() ?:
                                null;

            $methodName = $action ? getMethodName($action) : null;

            return $methodName ?: null;
        } catch(Exception $exception) {

        }

        return null;
    }
}

if( !function_exists('currentController') ) {
    /**
     * @return \Illuminate\Routing\Controller|null
     * @throws \Exception
     */
    function currentController()
    {
        $route = Route::current();
        if( !$route ) return null;

        if( isset($route->controller) || method_exists($route, 'getController') ) {
            return isset($route->controller) ? $route->controller : $route->getController();
        }

        $action = $route->getAction();
        if( $action && isset($action[ 'controller' ]) ) {
            $currentAction = $action[ 'controller' ];
            [ $controller, $method ] = explode('@', $currentAction);

            return $controller ? app($controller) : null;
        }

        return null;
    }
}

if( !function_exists('validUrl') ) {
    /**
     * @param string|null $url
     *
     * @return \Laravel\Nova\URL|string|null
     */
    function validUrl(string|null $url = null): URL|string|null
    {
        return isValidUrl($url) ? URL::remote($url) : $url;
    }
}
