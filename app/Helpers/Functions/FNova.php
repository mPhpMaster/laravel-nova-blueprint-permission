<?php

if( !function_exists('isRequestNovaIndex') ) {
    function isRequestNovaIndex(\Illuminate\Http\Request $request): bool
    {
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
