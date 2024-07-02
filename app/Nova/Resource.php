<?php

namespace App\Nova;

use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource as NovaResource;
use Nova;

/**
 *
 */
abstract class Resource extends NovaResource
{
    /**
     * The per-page options used the resource index.
     *
     * @var array
     */
    public static $perPageOptions = [ 25, 50, 100, 500, 1000 ];

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Other';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * Build an "index" query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Scout\Builder                  $query
     *
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }

    /**
     * Get the text for the create resource button.
     *
     * @return string|null
     */
    public static function createButtonLabel()
    {
        return __('Create :resource', [ 'resource' => static::singularLabel() ]);
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            ExportAsCsv::make(__('Export as CSV'))
                       ->nameable(snake_case(class_basename(static::class) . '-' . now(), '-'))
                       ->withFormat(fn($model) => $model->toFormattedArray()),
        ];
    }

    /**
     * Check if the current resource equal to this resource.
     *
     * @return bool
     */
    public static function isCurrent(): bool
    {
        return ($currentResource = request('view')) &&
            method_exists(static::class, 'uriKey') &&
            $currentResource === 'resources/' . static::uriKey();
    }

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return __(static::$group);
    }

    /**
     * Get the displayable label of navigation.
     *
     * @return string
     */
    public static function navigationLabel()
    {
        $label = static::label();

        return $label;
    }

    /**
     * @return string
     */
    public static function menuLabel()
    {
        return static::navigationLabel();
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        $name = Str::singular((Str::snake(class_basename(static::class), ' ')));
        return static::trans($name);
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        $name = Str::plural((Str::snake(class_basename(static::class), ' ')));
        return static::trans($name);
    }

    /**
     * @param $key
     * @param $default
     *
     * @return false|string[]
     */
    public static function getRules($key = null, $default = null, ...$merge)
    {
        $rules = getRules(static::$model, $key, $default);

        return !empty($merge) ? array_merge((array) $rules, ...$merge) : $rules;
    }

    /**
     * alias for __("models/model_name") and __("models/model_name.fields.field_name")
     *
     * @param string               $key
     * @param array                $replace
     * @param string|null          $locale
     * @param string|\Closure|null $default
     *
     * @return array|string|null
     *
     * @see \App\Traits\ModelTranslationTrait::trans()
     */
    public static function trans($key = null, $replace = [], $locale = null, $default = null)
    {
        $locale = $locale ?? currentLocale();
        $default = $default ?? __($key, $replace, $locale);

        /** @var $class \App\Models\Model */
        return with(static::$model, fn($class) => $class::trans($key, $replace, $locale, $default));
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest|null $request
     *
     * @return string
     */
    public static function getResourceIndexUrl(NovaRequest $request = null)
    {
        return '/resources/' . static::uriKey();
    }

    /**
     * Return the location to redirect the user after update.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Nova\Resource                  $resource
     * @param string|null                             $path
     *
     * @return string
     */
    public static function getResourceUrl(NovaRequest $request, $resource, ?string $path = null)
    {
        return ($path ?? Nova::path()) . "" . static::getResourceIndexUrl($request) . '/' . $resource->getKey();
    }

}
