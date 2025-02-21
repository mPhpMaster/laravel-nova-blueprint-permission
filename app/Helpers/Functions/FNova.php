<?php

use Illuminate\Support\Collection;
use Laravel\Nova\Asset;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FormData;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Style;

if( !function_exists('isRequestNovaIndex') ) {
	function isRequestNovaIndex(\Illuminate\Http\Request $request = null): bool
    {
		$request ??= getNovaRequest();
        return $request instanceof \Laravel\Nova\Http\Requests\ResourceIndexRequest;
    }
}

if( !function_exists('isRequestNovaDetail') ) {
    function isRequestNovaDetail(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\ResourceDetailRequest;
    }
}

if( !function_exists('isRequestNovaCreate') ) {
    function isRequestNovaCreate(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\NovaRequest &&
            $request->editMode === 'create';
    }
}

if( !function_exists('isRequestNovaUpdate') ) {
    function isRequestNovaUpdate(\Illuminate\Http\Request $request): bool
    {
        return $request instanceof \Laravel\Nova\Http\Requests\NovaRequest &&
            $request->editMode === 'update';
    }
}

if( !function_exists('getNovaResourceId') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     *
     * @return int|double|string|mixed|null
     */
    function getNovaResourceId(\Illuminate\Http\Request $request = null)
    {
        $resourceId = ($request ??= request())->resourceId;
        if( is_numeric($resourceId) ) {
            $resourceId += 0;
        }

        return $resourceId;
    }
}

if( !function_exists('getNovaParentResource') ) {
    /**
     * returns the parent resource for which the current item is being created.
     *
     * @param \Illuminate\Http\Request|null $request
     *
     * @return string|null
     */
    function getNovaParentResource(\Illuminate\Http\Request $request = null): ?string
    {
        return ($request ??= request())->viaResource;
    }
}

if( !function_exists('getNovaParentResourceId') ) {
    /**
     * returns the parent resource id for which the current item is being created.
     *
     * @param \Illuminate\Http\Request|null $request
     *
     * @return int|double|string|mixed|null
     */
    function getNovaParentResourceId(\Illuminate\Http\Request $request = null)
    {
        $resourceId = ($request ??= request())->viaResourceId;
        if( is_numeric($resourceId) ) {
            $resourceId += 0;
        }

        return $resourceId;
    }
}

if( !function_exists('currentNovaResourceClass') ) {
    /**
     * Get Nova Resource Class through debug backtrace.
     *
     * @param \Closure|null $callback
     *
     * @return string|\App\Nova\Resource|null
     */
    function currentNovaResourceClass(\Closure $callback = null): ?string
    {
        $class = null;
        foreach( ($debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)) as $index => $item ) {
            if( isset($item[ 'class' ]) && $item[ 'class' ] == \Laravel\Nova\Resource::class ) {
                $class = data_get($debug, ($index - 1) . ".class");
                break;
            }
        }

        if( is_null($class) ) {
            foreach( $debug as $index => $item ) {
                if( isset($item[ 'class' ]) && is_subclass_of($item[ 'class' ], \Laravel\Nova\Resource::class) ) {
                    $class = data_get($item, "class");
                    break;
                }
            }
        }

        return with($class ?? getNovaResource(), $callback ?? fn($model) => $model);
    }
}

if( !function_exists('currentNovaResourceClassCalled') ) {
    /**
     * Get Nova Resource Class through debug backtrace.
     *
     * @param \Closure|null $callback
     *
     * @return string|\App\Nova\Resource|null
     */
    function currentNovaResourceClassCalled(\Closure $callback = null): ?string
    {
        $class = null;
        foreach( ($debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)) as $index => $item ) {
            if( isset($item[ 'class' ]) && $item[ 'class' ] == \Laravel\Nova\Resource::class ) {
                $class = data_get($debug, ($index - 1) . ".class");
                break;
            }
        }

        if( is_null($class) ) {
            foreach( $debug as $index => $item ) {
                if( isset($item[ 'class' ]) && is_subclass_of($item[ 'class' ], \Laravel\Nova\Resource::class) ) {
                    $class = data_get($item, "class");
                    break;
                }
            }
        }

        return with($class, $callback ?? fn($model) => $model);
    }
}

if( !function_exists('currentNovaResourceModelClass') ) {
    /**
     * Get Nova Resource Model Class through debug backtrace.
     *
     * @param \Closure|null $callback
     *
     * @return string|\App\Models\Model|null
     */
    function currentNovaResourceModelClass(\Closure $callback = null): ?string
    {
        return with(
            currentNovaResourceClass(\Closure::fromCallable('resourceModelExtractor')),
            $callback ?? fn($model) => $model
        );
    }
}

if( !function_exists('resourceModelExtractor') ) {
    /**
     * Get Nova Resource Model Class through resource class.
     *
     * @param \App\Nova\Resource|string $resource
     *
     * @return string|\App\Models\Model|null
     */
    function resourceModelExtractor($resource)
    {
        return class_exists($resource) ? ($resource::$model ?? null) : null;
    }
}

if( !function_exists('getNovaRequest') ) {
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Laravel\Nova\Http\Requests\NovaRequest|mixed
     */
    function getNovaRequest()
    {
        return app(\Laravel\Nova\Http\Requests\NovaRequest::class);
    }
}

if( !function_exists('getNovaResourceInfoFromRequest') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     * @param string|null                   $key
     *
     * @return array|string|null|mixed
     */
    function getNovaResourceInfoFromRequest(?\Illuminate\Http\Request $request = null, ?string $key = null)
    {
        $resourceInfo = [
            'resource' => null,
            'resourceName' => null,
            'resourceId' => null,
            'mode' => null,
        ];

        try {
            $request ??= getNovaRequest();

            if( $request->segment(2) === 'resources' ) {
                $resourceInfo = [
                    'resource' => Nova::resourceForKey($resourceName = $request->segment(3)),
                    'resourceName' => $resourceName,
                    'resourceId' => $resourceId = $request->segment(4),
                    'mode' => $request->segment(5) ?? ($resourceId ? 'view' : 'index'),
                ];
            }
        } catch(Exception $exception) {
        }

        return is_null($key) ? $resourceInfo : data_get($resourceInfo, $key);
    }
}

if(!function_exists('NovaStatusField')) {
	/**
	 * @param array $options
	 *
	 * @return \Laravel\Nova\Fields\Select
	 */
	function NovaStatusField(
		array $config = [
			'name'          => null,
			'attribute'     => null,
			'trans'         => null,
			'options'       => null,
			'options_trans' => null,
			'default'       => STATUS_ACTIVE,
		],
	): Select
	{
		$config['options_trans'] ??= $config['trans'] ?? null;

		[
			'name'          => $name,
			'attribute'     => $attribute,
			'trans'         => $trans,
			'options_trans' => $options_trans,
			'options'       => $options,
			'default'       => $default,
		] = $config;

		/** @var string $attribute */
		$attribute = value($attribute ?: $name);

		/** @var \Closure $trans */
		$trans = $trans && (isClosure($trans) || is_callable($trans)) ?
			$trans :
			(fn(...$a) => $a[0] ?? $a [1] ?? null);
		/** @var \Closure $options_trans */
		$options_trans = $options_trans && (isClosure($options_trans) || is_callable($options_trans)) ?
			$options_trans : (
			$trans ?: (fn(...$a) => $a[0] ?? $a [1] ?? null)
			);

		$options = $options ?: $options_trans(str_plural(snake_case($attribute)));
		/** @var array $options */
		$options = array_wrap(is_string($options) ? $options_trans($options) : $options);

		/** @var (\Closure(\Laravel\Nova\Http\Requests\NovaRequest):mixed)|mixed $default */
		$default = value($default) ?: STATUS_ACTIVE;

		return Select::make($trans($name), $attribute)
			->options(fn() => $options)
			->default(fn() => $default)
			->sortable()
			->displayUsingLabels();
	}
}

if( !function_exists('getNovaRequestParameters') ) {
    /**
     * @param \Illuminate\Http\Request|null $request
     * @param array|string|null             $key
     *
     * @return array|object|string|null|mixed
     */
    function getNovaRequestParameters(?\Illuminate\Http\Request $request = null, $key = null)
    {
        $results = [];
        try {
            $request ??= getNovaRequest();

            /** @var \Illuminate\Routing\Route $route */
            $route = call_user_func($request->getRouteResolver());

            if( is_null($route) ) {
                return $results;
            }

            $results = $route->parameters();
            if( is_null($key) ) {
                if( is_array($results) && isset($results[ 'resource' ]) ) {
                    $results[ 'resource_class' ] = Nova::resourceForKey($results[ 'resource' ]);
                    $results[ 'resource_model' ] = Nova::modelInstanceForKey($results[ 'resource' ]);
                    $results[ 'model' ] = fn() => isset($results[ 'resourceId' ]) && isset($results[ 'resource_model' ]) && class_exists($results[ 'resource_model' ]) ?
                        $results[ 'resource_model' ]::find($results[ 'resourceId' ]) : null;
                }
            } else {
                $key = (array) $key;
                $results = blank($key) ? $results : array_only($results, $key);
            }

        } catch(Exception $exception) {
        }

        return $results;
    }
}

if( !function_exists('getNovaResource') ) {
    /**
     * Get current browsing nova resource via link.
     *
     * @return string|null
     */
    function getNovaResource(): ?string
    {

        try {
            $resource = getNovaResourceInfoFromRequest(null, 'resource');
        } catch(Exception $exception) {
            $resource = null;
        }

        return $resource;
    }
}

if( !function_exists('isCurrentResource') ) {
    /**
     * Check if current resource is the given resource.
     *
     * @param string $resource
     *
     * @return bool
     */
    function isCurrentResource(string $resource): bool
    {
        return ($currentResource = request('view')) &&
            class_exists($resource) &&
            method_exists($resource, 'uriKey') &&
            $currentResource === 'resources/' . $resource::uriKey();
    }
}

if( !function_exists('getNovaResources') ) {
    /**
     * Get All nova resources.
     *
     * @param string $app_dir
     * @param string $parent_class
     *
     * @return array
     */
    function getNovaResources(string $app_dir = 'Nova', string $parent_class = \App\Nova\Resource::class): array
    {
        $resources = glob(app_path($app_dir) . DIRECTORY_SEPARATOR . '*.php');

        return toCollect($resources)
            ->map(function($resource) use ($parent_class) {
                $resource_class = str_ireplace(
                    [ "/", ".php" ],
                    [ "\\", "" ],
                    "App" . str_after($resource, app_path())
                );
                $is_nova_resource =
                    $resource_class !== $parent_class && class_exists($resource_class) && is_subclass_of(
                        $resource_class,
                        $parent_class
                    );

                return $is_nova_resource ? $resource_class : null;
            })
            ->filter()
            ->toArray();
    }
}

if( !function_exists('getNovaResourcesAsOptions') ) {
    /**
     * Get All nova resources as options.
     *
     * @param string $app_dir
     * @param string $parent_class
     *
     * @return array
     */
    function getNovaResourcesAsOptions(
        string $app_dir = 'Nova',
        string $parent_class = \App\Nova\Resource::class
    ): array {
        return collect(getNovaResources($app_dir, $parent_class))
            ->filter(fn($f) => class_exists($f) && is_subclass_of($f, $parent_class))
            ->mapWithKeys(fn($r) => [
                /** @var \App\Nova\Resource $r */
                $r::singularLabel() => $r::singularLabel() . ' - ' . $r::label(),
            ])
            ->toArray();
    }
}

if( !function_exists('getNovaResourcesDependencies') ) {
    /**
     * Get All nova resources Dependencies.
     *
     * @param array|null $options
     *
     * @return array
     */
    function getNovaResourcesDependencies(
        ?string $field = null,
        ?array $options = null
    ): array {
        $options = (array) ($options ?? getNovaResourcesAsOptions());
        $dependencies = [];

        foreach( $options as $option => $label ) {
            $n = "text";
            $dependencies[] = \Epartment\NovaDependencyContainer\NovaDependencyContainer::make([
                                                                                                   \Laravel\Nova\Fields\Text::make(($text = "{$option} Text ") . $n, $n),
                                                                                               ])->dependsOn($field, $option);
        }

        return $dependencies;

        return collect(getNovaResources($app_dir, $parent_class))
            ->filter(fn($f) => class_exists($f) && is_subclass_of($f, $parent_class))
            ->mapWithKeys(fn($r) => [
                /** @var \App\Nova\Resource $r */
                $r::singularLabel() => $r::singularLabel() . ' - ' . $r::label(),
            ])
            ->toArray();
    }
}

if( !function_exists('getDefaultFromDate') ) {
    /**
     * @return \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon
     */
    function getDefaultFromDate()
    {
        return now()->firstOfYear();
    }
}

if( !function_exists('getDefaultToDate') ) {
    /**
     * @return \Carbon\Carbon|\Carbon\CarbonPeriod|\DateTime|\Illuminate\Support\Carbon
     */
    function getDefaultToDate()
    {
        return now()->endOfYear();
    }
}

if( !function_exists('HiddenField') ) {
    /**
     * @param string|\Closure     $attribute
     * @param \Closure|mixed|null $value
     *
     * @return \Laravel\Nova\Fields\Hidden
     */
    function HiddenField($attribute, $value = null)
    {
        return \Laravel\Nova\Fields\Hidden::make($attribute = value($attribute), $attribute)->withMeta([ 'value' => value($value) ]);
    }
}

if(!function_exists('IDNovaField')) {
	/**
	 * @param string $name
	 * @param string $attribute
	 *
	 * @return \Laravel\Nova\Fields\ID
	 */
	function IDNovaField($name = '#', $attribute = 'id'): \Laravel\Nova\Fields\ID
	{
		return \Laravel\Nova\Fields\ID::make($name, $attribute)
			->sortable()/*->hideFromIndex()*/ ;
	}
}

if(!function_exists('BooleanNovaField')) {
	/**
	 * @param string      $name
	 * @param string|null $attribute
	 * @param mixed       $trueValue
	 * @param mixed       $falseValue
	 *
	 * @return \Laravel\Nova\Fields\Boolean
	 */
	function BooleanNovaField(string $name, string|Closure|null $attribute = null, mixed $trueValue = 1, mixed $falseValue = 0): \Laravel\Nova\Fields\Boolean
	{
		return \Laravel\Nova\Fields\Boolean::make(getTrans($name, $name), is_null($attribute) ? $name : $attribute)
			->trueValue($trueValue)
			->falseValue($falseValue)
			->sortable();
	}
}

if(!function_exists('dependsOn')) {
	/**
	 * @param string        $key
	 * @param mixed|null    $value
	 * @param \Closure|null $pipe
	 *
	 * @return array
	 */
	function dependsOn(string|array $key, mixed $value = null, ?\Closure $pipe = null): array
	{
		if(is_array($key)) {
			[ $key => $value ] = $key;
		}

		$pipe ??= value(...);

		return [
			'type',
			function(Field $field, NovaRequest $request, FormData $formData) use ($pipe, $key, $value) {
				if(in_array($value, [
					$formData->get($key),
					$request->get($key),
				])) {
					$pipe($field->show());
				}
			},
		];
	}
}

if(!function_exists('getResourceAsset')) {
	/**
	 * @param string                     $name
	 * @param string                     $dir
	 * @param string                     $ext
	 * @param string|\Laravel\Nova\Asset $type
	 *
	 * @return \Laravel\Nova\Asset
	 */
	function getResourceAsset(string $name, string $dir, string $ext, string|Asset $type = Style::class): Asset
	{
		$dirs = explode("/", trim($dir));
		$label = array_pop($dirs);
		$dir = implode("/", $dirs);
		$dir = ($dir = trim($dir)) === '-' || empty($dir) ? "" : $dir."/";
		$ext = ($ext = trim($ext)) === '-' ? "" : ".".$ext;
		$name2 = ($dir.$label."/").$name.$ext;
		$name = $dir.$name.$ext;
		$name = file_exists(resource_path($name2)) ? $name2 : $name;

		abort_unless(file_exists(resource_path($name)), 404);

		return $type::make($label ?: basename($name), resource_path($name));
	}
}

if(!function_exists('SelectNovaField')) {
	/**
	 * @param string                                                                                          $name
	 * @param string|null                                                                                     $attribute
	 * @param callable|\Closure|\Illuminate\Support\Collection|array<string|int, array<string, mixed>|string> $options
	 *
	 * @return Select
	 */
	function SelectNovaField(
		string                            $name,
		string|null                       $attribute = null,
		array|callable|Closure|Collection $options = [],
	): Select
	{
		return Select::make(getTrans($name, $name), is_null($attribute) ? $name : $attribute)
			->options($options)
			->sortable()
			->filterable()
//			->searchable()
			->displayUsingLabels();
	}
}