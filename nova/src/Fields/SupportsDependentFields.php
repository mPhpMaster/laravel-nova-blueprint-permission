<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Http\Requests\NovaRequest;

trait SupportsDependentFields
{
    /**
     * List of field dependencies.
     *
     * @var array<int, \Laravel\Nova\Fields\Dependent>
     */
    protected $fieldDependencies = [];

    /**
     * Register depends on to a field.
     *
     * @param  string|\Laravel\Nova\Fields\Field|array<int, string|\Laravel\Nova\Fields\Field>  $attributes
     * @param  (callable(static, \Laravel\Nova\Http\Requests\NovaRequest, \Laravel\Nova\Fields\FormData):(void))|class-string  $mixin
     * @return $this
     */
    public function dependsOn($attributes, $mixin)
    {
        array_push($this->fieldDependencies, new Dependent($attributes, $mixin));

        return $this;
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
//        if( is_array($key) ) {
//            [ $key => $value ] = $key;
//        }
        $pipe ??= value(...);

        return $this->dependsOn(
            $key,
            function (Field $field, NovaRequest $request, FormData $formData) use ($pipe, $key, $value) {
                foreach ((array)$value as $_value) {
                    $pass = false;
                    if ($_value instanceof \Closure) {
                        $pass = (bool)$_value($field, $request, $formData);
                    } else if (in_array($_value, [$formData->get($key), $request->get($key),])) {
                        $pass = true;
                    }

                    if ($pass) {
                        $pipe($field->show());
                    }
                }
            },
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
        $pipe ??= value(...);
        $originalConditions = $conditions->copy();
        $conditions = $originalConditions->toArray();
        $key = null;
        if (array_is_list($conditions)) {
            foreach ($conditions as $i => $condition) {
                throw_if(!is_array($condition) || array_is_list($condition), new \Exception("Condition must be array of arrays!"));
                $key ??= head(array_keys($condition));
                $conditions[$i] = array_map(fn($v) => array_wrap($v), $condition);
            }
        } else {
            $_conditions = [];
            foreach ($conditions as $i => $condition) {
                if (!is_int($i)) {
                    $condition = [$i => array_wrap($condition)];
                }
                $_conditions[] = array_wrap($condition);
            }
            $conditions = $_conditions;
            $key ??= head(array_keys(head($conditions)));
        }
        $attribute = array_unique(array_flatten(array_map(fn($v) => array_keys(array_wrap($v)), $conditions)));
        $attribute = count($attribute) === 1 ? head($attribute) : $attribute;
        $attributeattribute = $this->attribute;
        return $this->dependsOn(
            $attribute,
            function (Field $field, NovaRequest $request, FormData $formData) use ($attributeattribute, $originalConditions, $pipe, $key, $conditions) {
                $conditionsPass = array_map(fn($v) => [], $conditions);

                foreach ($conditions as $index => $condition) {
                    $condition = (array)$condition;
                    $pass = array_map(fn($v) => false, $condition);
                    $conditionsPass[$index] = &$pass;

                    foreach ($condition as $column => $values) {
                        $pass[$i = $column] = false;

                        // useless
                        if (array_is_list($condition)) {
                            $column = $key;
                        }

                        $testedValue = $formData->get($column) ?? $request->get($column);
                        if (count((array)$values) === 1 && head((array)$values) === null) {
                            $is_passed = !!$testedValue;
                        } else {
                            $is_passed = in_array($testedValue, (array)$values);
                        }

                        $pass[$i] = $originalConditions->isNot($index) ? !$is_passed : $is_passed;
                    }
                    unset($pass);

                    $conditionsPass[$index] = count(array_filter($conditionsPass[$index])) === count($conditionsPass[$index]);
                }

                if (count(array_filter($conditionsPass))) {
                    $pipe($field->show());

					foreach ($conditionsPass as $index => $__) {
						$originalConditions->doClosures($index, $field, $request, $formData);
					}
				}
			},
		);
	}

    /**
     * Register depends on to a field on creating request.
     *
     * @param  string|\Laravel\Nova\Fields\Field|array<int, string|\Laravel\Nova\Fields\Field>  $attributes
     * @param  (callable(static, \Laravel\Nova\Http\Requests\NovaRequest, \Laravel\Nova\Fields\FormData):(void))|class-string  $mixin
     * @return $this
     */
    public function dependsOnCreating($attributes, $mixin)
    {
        array_push($this->fieldDependencies, new Dependent($attributes, $mixin, 'create'));

        return $this;
    }

    /**
     * Register depends on to a field on updating request.
     *
     * @param  string|\Laravel\Nova\Fields\Field|array<int, string|\Laravel\Nova\Fields\Field>  $attributes
     * @param  (callable(static, \Laravel\Nova\Http\Requests\NovaRequest, \Laravel\Nova\Fields\FormData):(void))|class-string  $mixin
     * @return $this
     */
    public function dependsOnUpdating($attributes, $mixin)
    {
        array_push($this->fieldDependencies, new Dependent($attributes, $mixin, 'update'));

        return $this;
    }
}
