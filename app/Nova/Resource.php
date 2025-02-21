<?php

namespace App\Nova;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ExportAsCsv;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource as NovaResource;
use Laravel\Scout\Builder as ScoutBuilder;

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
     */
    public static function indexQuery(NovaRequest $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     */
    public static function scoutQuery(NovaRequest $request, ScoutBuilder $query): ScoutBuilder
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     */
    public static function detailQuery(NovaRequest $request, Builder $query): Builder
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     */
    public static function relatableQuery(NovaRequest $request, Builder $query): Builder
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
	 * Check if the current resource equal to this resource.
	 *
	 * @return bool
	 */
	public static function isCurrent(): bool
	{
		return ($currentResource = request('view')) &&
			method_exists(static::class, 'uriKey') &&
			$currentResource === 'resources/'.static::uriKey();
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

	public static function label()
	{
		$name = Str::plural((Str::snake(class_basename(static::class), ' ')));
		return static::trans($name);
		return static::trans('plural');
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
		return static::trans('singular');
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
		return '/resources/'.static::uriKey();
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
	public static function getResourceUrl(NovaRequest $request, $resource, ?string $path = null, string $append = '')
	{
		return ($path ?? Nova::path()).''.static::getResourceIndexUrl($request).'/'.$resource->getKey().($append ? "/{$append}" : '');
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
				->nameable(snake_case(class_basename(static::class).'-'.now()->format('YmdHis'), '-'))
				->withFormat(fn($model) => $model->toFormattedArray())
				->withTypeSelector(),
		];
	}

	public function hideWhen(string $type, mixed $needles, Request $request = null, bool $not = false, ?\Closure $pipe = null, mixed $with = null): \Closure
	{
		$request ??= getNovaRequest();
		$with ??= $this->model();
		$pipe ??= fn($r) => $r;
		return function() use ($with, $not, $request, $type, $needles, $pipe) {
			$value = in_array($request->get($type) ?: $this->$type, (array) $needles);
			$result = $not ? !$value : $value;
			$results = $pipeResult = $pipe(...[ $result, $with, $value, $type, $needles, $request ]);

			return $results;
		};
	}

	public function hideWhenNot(string $type, mixed $needles, Request $request = null, bool $not = true, ?\Closure $pipe = null, mixed $with = null): \Closure
	{
		return $this->hideWhen($type, $needles, $request, $not, $pipe, $with);
	}

	/**
	 * @param string                            $name
	 * @param (\Closure():array|iterable)|array $fields
	 * @param \Closure|null                     $pipe
	 *
	 * @return MergeValue
	 */
	public function newPanel($name, $fields = [], ?\Closure $pipe = null): MergeValue
	{
		$pipe ??= value(...);
		return $this->merge([
			$pipe(Panel::make($name, $fields))
		]);
	}

	public function createdAtField(NovaRequest|Request $request): DateTime
	{
		return DateTime::make(static::trans('created_at'), 'created_at')
			->sortable()
			->filterable()
			->showWhenPeeking()
			->exceptOnForms()
			->hideFromIndex();
	}

	public function updatedAtField(NovaRequest|Request $request): DateTime
	{
		return DateTime::make(static::trans('updated_at'), 'updated_at')
			->sortable()
			->filterable()
			->showWhenPeeking()
			->exceptOnForms();
	}

}
