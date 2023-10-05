<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;
use Laravel\Nova\Contracts\FilterableField;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\Filters\TextFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

class Text extends Field implements FilterableField
{
    use FieldFilterable;
    use HasSuggestions;
    use SupportsDependentFields;
    use SupportsMaxlength;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'text-field';

    /**
     * Indicates if the field value should be displayed as HTML.
     *
     * @var bool
     */
    public $asHtml = false;

    /**
     * Indicates if the field value is copyable inside Nova.
     *
     * @var bool
     */
    public $copyable = false;

    /**
     * Create a new field.
     *
     * @param string                                       $name
     * @param string|\Closure|callable|object|null         $attribute
     * @param (callable(mixed, mixed, ?string):mixed)|null $resolveCallback
     *
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        if( static::class === self::class ) {
            $this
                ->maxlength(\Illuminate\Database\Schema\Builder::$defaultStringLength, true)
                ->displayUsing(fn($value) => str_limit($value, 50));
        }
    }

    /**
     * Display the field as raw HTML using Vue.
     *
     * @return $this
     */
    public function asHtml()
    {
        if ($this->copyable) {
            throw new HelperNotSupported("The `asHtml` option is not available on fields set to `copyable`. Please remove the `copyable` method from the {$this->name} field to enable `asHtml`.");
        }

        $this->asHtml = true;

        return $this;
    }

    /**
     * Allow the field to be copyable to the clipboard inside Nova.
     *
     * @return $this
     */
    public function copyable()
    {
        if ($this->asHtml) {
            throw new HelperNotSupported(sprintf("The `%s` option is not available on fields displayed as HTML. Please remove the `asHtml` method from the {$this->name} field to enable `copyable`.",
                __METHOD__));
        }

        $this->copyable = true;

        return $this;
    }

    /**
     * Make the field filter.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return \Laravel\Nova\Fields\Filters\Filter
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new TextFilter($this);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function serializeForFilter()
    {
        return transform($this->jsonSerialize(), function ($field) {
            $field['suggestions'] = $field['suggestions'] ?? $this->resolveSuggestions(app(NovaRequest::class));

            return Arr::only($field, [
                'uniqueKey',
                'name',
                'attribute',
                'suggestions',
                'type',
                'min',
                'max',
                'step',
                'pattern',
                'placeholder',
                'extraAttributes',
            ]);
        });
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(NovaRequest::class);

        if ($request->isFormRequest()) {
            return array_merge(parent::jsonSerialize(), [
                'suggestions' => $this->resolveSuggestions($request),
            ]);
        }

        return array_merge(parent::jsonSerialize(), [
            'asHtml' => $this->asHtml,
            'copyable' => $this->copyable,
        ]);
    }
}
