<?php

namespace Laravel\Nova;

use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use App\Helpers\Classes\FieldDependency;
use Illuminate\Http\Resources\MergeValue;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Fields\Collapsable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\HasHelpText;

/**
 * @phpstan-type TFields \Laravel\Nova\Fields\Field|\Laravel\Nova\ResourceToolElement|\Illuminate\Http\Resources\MergeValue|\Illuminate\Http\Resources\MissingValue
 * @phpstan-type TPanelFields array<int, TFields>|iterable<int, TFields>
 *
 * @method static static make(string $name, \Closure|array|iterable $fields = [])
 */
#[\AllowDynamicProperties]
class Panel extends MergeValue implements JsonSerializable
{
    use ConditionallyLoadsAttributes;
    use Macroable;
    use Metable;
    use Makeable;
    use HasHelpText;
    use Collapsable;

    /**
     * The name of the panel.
     *
     * @var string
     */
    public $name;

    /**
     * The panel fields.
     *
     * @var array<int, \Laravel\Nova\Fields\Field>
     */
    public $data;

    /**
     * The panel's component.
     *
     * @var string
     */
    public $component = 'panel';

    /**
     * Indicates whether the detail toolbar should be visible on this panel.
     *
     * @var bool
     */
    public $showToolbar = false;

    /**
     * The initial field display limit.
     *
     * @var int|null
     */
    public $limit = null;

    /**
     * The help text for the element.
     *
     * @var string
     */
    public $helpText;
	protected bool $hide = false;

    /**
     * Create a new panel instance.
     *
     * @param  string  $name
     * @param  (\Closure():(object))|object  $fields
     * @return void
     *
     * @phpstan-param (\Closure():(TPanelFields))|TPanelFields $fields
     */
    public function __construct($name, $fields = [])
    {
        $this->name = $name;

        parent::__construct($this->prepareFields($fields));
    }

    /**
     * Mutate new panel from list of fields.
     *
     * @param  string  $name
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return static
     */
    public static function mutate($name, $fields)
    {
        $first = $fields->first();

        if ($first instanceof ResourceToolElement) {
            return static::make($name)
                ->withComponent($first->component)
                ->withMeta(['fields' => $fields, 'prefixComponent' => false]);
        }

        return tap($first->assignedPanel, function ($panel) use ($name, $fields) {
            $panel->name = $name;
            $panel->withMeta(['fields' => $fields]);
        });
    }

    /**
     * Prepare the given fields.
     *
     * @param  (\Closure():(object))|object  $fields
     * @return array<int, \Laravel\Nova\Fields\Field>
     *
     * @phpstan-param (\Closure():(TPanelFields))|TPanelFields $fields
     */
    protected function prepareFields($fields)
    {
        $fields = is_callable($fields) ? $fields() : $fields;

        return collect($this->filter($fields instanceof Collection ? $fields->all() : $fields))
            ->reject(function ($field) {
                return $field instanceof MissingValue;
            })
            ->values()
            ->each(function ($field) {
                $field->assignedPanel = $this;
                $field->panel = $this->name;
            })->all();
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameForDetail(Resource $resource)
    {
        return Nova::__(':resource Details: :title', [
            'resource' => $resource->singularLabel(),
            'title' => (string) $resource->title(),
        ]);
    }

    /**
     * Get the default panel name for a create panel.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameForCreate(Resource $resource)
    {
        return Nova::__('Create :resource', [
            'resource' => (string) $resource->singularLabel(),
        ]);
    }

    /**
     * Get the default panel name for the update panel.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function defaultNameForUpdate(Resource $resource)
    {
        return Nova::__('Update :resource: :title', [
            'resource' => $resource->singularLabel(),
            'title' => $resource->title(),
        ]);
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    public static function defaultNameForViaRelationship(Resource $resource, NovaRequest $request)
    {
        $field = $request->newViaResource()
            ->availableFields($request)
            ->filter(function ($field) use ($request) {
                return $field instanceof RelatableField
                    && $field->resourceName === $request->resource
                    && $field->relationshipName() === $request->viaRelationship;
            })->first();

        return $field->name;
    }

    /**
     * Display the toolbar when showing this panel.
     *
     * @return $this
     */
    public function withToolbar()
    {
        $this->showToolbar = true;

        return $this;
    }

    /**
     * Set the number of initially visible fields.
     *
     * @param  int  $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set the Vue component key for the panel.
     *
     * @param  string  $component
     * @return $this
     */
    public function withComponent($component)
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Get the Vue component key for the panel.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Set the width for the help text tooltip.
     *
     * @param  string  $helpWidth
     * @return $this
     *
     * @throws \Exception
     */
    public function helpWidth($helpWidth)
    {
        throw new \Exception('Help width is not supported on panels.');
    }

    /**
     * Return the width of the help text tooltip.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getHelpWidth()
    {
        throw new \Exception('Help width is not supported on panels.');
    }

    /**
     * Prepare the panel for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'collapsable' => $this->collapsable,
            'collapsedByDefault' => $this->collapsedByDefault,
            'component' => $this->component(),
            'name' => $this->name,
            'showToolbar' => $this->showToolbar,
            'limit' => $this->limit,
            'helpText' => $this->getHelpText(),
        ], $this->meta());
    }

	public function hide(bool $hide = true): static
	{
		$this->hide = $hide;
		$method = $hide ? 'hide' : 'show';
		return $this->setData(collect($this->data)->map(fn(Field $field) => $field->$method()));
	}

	public function show(bool $show = true): static
	{
		$this->hide = !$show;
		$method = !$show ? 'hide' : 'show';

		return $this->setData(collect($this->data)->map(fn(Field $field) => $field->$method()));
	}

	/**
	 * @param (\Closure():array|iterable)|array|iterable $data
	 * @return static
	 */
	public function setData($data)
	{
		$data = $this->prepareFields($data);

		if ($data instanceof Collection) {
			$this->data = $data->all();
		} elseif ($data instanceof JsonSerializable) {
			$this->data = $data->jsonSerialize();
		} else {
			$this->data = $data;
		}

		return $this;
	}

	/**
	 * Specify that the element should be hidden from the detail view.
	 *
	 * @param  (callable():bool)|bool  $callback
	 * @return $this
	 */
	public function hideFromDetail($callback = true)
	{
		return $this->setData(
			collect($this->data)->map(fn(Field $field) => $field->hideFromDetail($callback))
		);
	}

	/**
	 * Specify that the element should be hidden from the index view.
	 *
	 * @param  (callable():bool)|bool  $callback
	 * @return $this
	 */
	public function hideFromIndex($callback = true)
	{
		return $this->setData(
			collect($this->data)->map(fn(Field $field) => $field->hideFromIndex($callback))
		);
	}

	public function dependsOn($attributes, $mixin)
	{
		return $this->setData(
			collect($this->data)->map(fn(Field|Panel $field) => $field->dependsOn($attributes, $mixin))
		);
	}

	/**
	 * @param string $key
	 * @param mixed|null $value
	 * @param \Closure|null $pipe
	 *
	 * @return $this
	 */
	public function dependsOnWithPipe(string|array $key, mixed $value = null, ?\Closure $pipe = null)
	{
		return $this->setData(
			collect($this->data)->map(fn(Field $field) => $field->dependsOnWithPipe($key, $value, $pipe))
		);
	}

	/**
	 * @param string $key
	 * @param mixed|null $value
	 * @param \Closure|null $pipe
	 *
	 * @return $this
	 */
	public function dependsOnMultiple(FieldDependency $conditions, ?\Closure $pipe = null)
	{
		return $this->setData(
			collect($this->data)->map(fn(Field $field) => $field->dependsOnMultiple($conditions,$pipe))
		);
	}
}
