<?php

namespace App\Models\Abstracts;

use App\Models\Scopes\Searchable;
use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperModel
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;
    use TModelTranslation;
    use Searchable;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    // protected $dateFormat = 'Y-m-d h:i:s a';
    protected $dateFormat;

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        // $this->dateFormat = getDefaultDateFormat();
        parent::__construct($attributes);
    }

    /**
     * @var array
     */
    public static $rules = [];

    /**
     * @param $key
     * @param $value
     *
     * @return bool|\Illuminate\Support\Collection|int|mixed|string|null
     */
    public static function castSingleAttribute($key, $value)
    {
        return static::make()->forceFill([ $key => $value ])->castAttribute($key, $value);
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getOnlyDateFormat()
    {
        return str_before($this->getDateFormat(), " ");
    }
    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $parsedKey = camel_case($method = value($method));
        if( $this->isRelation($parsedKey) ) {
            return parent::__call($parsedKey, $parameters);
        }

        if( ends_with($parsedKey, 'WithTrashed') ) {
            $parsedKeyRelation = str_before($parsedKey, "WithTrashed");
            if( $this->isRelation($parsedKeyRelation) ) {
                if( $result = $this->$parsedKeyRelation() ) {
                    return $result->withTrashed();
                }

                return null;
            }
        } elseif( starts_with($parsedKey, 'by') ) {
            $scopeName = 'scope' . studly_case($parsedKey);
            if( method_exists(static::class, $scopeName) ) {
                return static::query()->$parsedKey(...$parameters);
            }
            $parsedKeyName = str_after($parsedKey, "by");
            $cases = [
                'snake_case',
                'camel_case',
                'title_case',
                'studly_case',
            ];
            $keys = [];
            foreach( $cases as $i => $case ) {
                $keys[ $parsedKeyName . $i . " {$case}" ] = $case($parsedKeyName);
                $keys[ $parsedKeyName . "_id" . $i . " {$case}" ] = $case($parsedKeyName . "_id");
                $keys[ $parsedKeyName . "_ID" . $i . " {$case}" ] = $case($parsedKeyName . "_ID");
            }
            $keys[] = studly_case($parsedKeyName) . "_ID";
            $keys[] = camel_case($parsedKeyName) . "_ID";
            $keys = array_values(array_unique($keys));
            foreach( $keys as $key ) {
                if( $this->isFillable($key) ) {
                    // if( method_exists($this, 'isTranslatableAttribute') && $this->isTranslatableAttribute($key) ) {
                    //     $query = $this->where(function($q) use ($parameters, $key) {
                    //         foreach( getLocales() as $locale ) {
                    //             $q = $q->orWhere("{$key}->{$locale}", ...$parameters);
                    //         }
                    //
                    //         return $q;
                    //     });
                    //
                    //     return $query;
                    // } else {
                    return $this->where($key, ...$parameters);
                    // }
                }
            }
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $parsedKey = camel_case($key = value($key));
        if( $this->isRelation($parsedKey) ) {
            return parent::__get($parsedKey);
        }

        if( ends_with($parsedKey, 'WithTrashed') ) {
            $parsedKeyRelation = str_before($parsedKey, "WithTrashed");
            if( $this->isRelation($parsedKeyRelation) ) {
                if( $result = $this->$parsedKeyRelation() ) {
                    $relation = $result->withTrashed();

                    return tap($relation->getResults(), function($results) use ($parsedKey) {
                        $this->setRelation($parsedKey, $results);
                    });
                }

                return null;
            }
        }

        if( ends_with($parsedKey, 'Count') ) {
            $parsedKeyRelation = str_before($parsedKey, "Count");
            if( $this->isRelation($parsedKeyRelation) ) {
                if( $result = $this->$parsedKeyRelation() ) {
                    return $result->count();
                }

                return null;
            }
        }
        return parent::__get($key);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return collect(parent::toArray())
            ->filter(fn($value, $key) => $this->isFillable($key) || $key === $this->getKeyName())
            ->all();
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toFillableArray(): array
    {
        return collect(parent::toArray())
            ->filter(fn($value, $key) => $this->isFillable($key) || $key === $this->getKeyName())
            ->all();
    }

    /**
     * @return array
     */
    public function toFormattedArray(): array
    {
        return collect($this->toArray())
            ->mapWithKeys(function($value, $key) {
                return [
                    static::trans($key) => $this->getFormattedAttribute($key, $value, 'Y-m-d H:i:s.v'),
                ];
            })
            ->all();
    }

    /**
     * @param $column
     * @param $value
     * @param $format
     *
     * @return mixed|string
     */
    public function getFormattedAttribute(string $column, $value = null, $format = null): mixed
    {
        $value ??= $this->getAttribute($column);
        $format ??= $this->getDateFormat();

        return $this->isDateAttribute($column) ? carbon()->parse($value)->format($format) : $value;
    }

    /**
     * Alias for toArray
     *
     * @return array
     */
    public function TA(): array
    {
        return $this->toArray();
    }

    /**
     * To disable/enable Foreign Key Constraints
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param bool                                  $disable
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoConstraints(Builder $builder, bool $disable = true)
    {
        $disable
            ? \Schema::disableForeignKeyConstraints()
            : \Schema::enableForeignKeyConstraints();

        return $builder;
    }

    /**
     * @param string|\Closure $relation_name
     * @param string|\Closure $attribute
     * @param mixed|null      $default
     *
     * @return mixed
     */
    public function getFromRelation($relation_name, $attribute, $default = null)
    {
        return getModelRelationAttribute($this, $relation_name, $attribute, $default);
    }

    /**
     * @param       $key
     * @param       $default
     * @param array $merge
     *
     * @return array|mixed|string[]
     */
    public static function getRules($key = null, $default = null, array $merge = [])
    {
        $model = static::class;
        $field = $key;
        $default = value($default);
        if( $rules = $model::$rules ?? $model::rules ?? [] ) {
            if( $field ) {
                if( $ruleField = $rules[ $field ] ?? null ) {
                    return explode('|', $ruleField) ?? $default;
                }

                return $default;
            }

            return $rules ?? $default;
        }

        return !empty($merge) ? array_merge((array) $rules, $merge) : $rules;
    }

    /**
     * @param array|string|null $attributes
     *
     * @return bool
     */
    public function isChanged($attributes = null): bool
    {
        return $this->isInstanceNew() || $this->wasChanged($attributes) || $this->isDirty($attributes);
    }

    /**
     * @return bool
     */
    public function isInstanceNew(): bool
    {
        return $this->wasRecentlyCreated;
    }

    /**
     * Fill attributes after filter them.
     * Support translatable
     *
     * @param array                    $attributes
     * @param \Closure|array|bool|null $filter
     *
     * @return self
     */
    public function fillAttributes($attributes, $filter = null, ?array $locales = null)
    {
        $closure = is_null($filter) ? (fn($v) => true) : null;
        $closure ??= is_bool($filter) ? (fn($v) => $v) : null;
        $closure ??= is_array($filter) ? (fn($v) => !in_array($v, $filter)) : null;
        $closure ??= isClosure($filter) ? $filter : null;

        if( !is_null($closure) ) {
            $attributes = array_filter($attributes, $closure);
        }

        foreach( $attributes as $attribute => $value ) {
            /** @var \App\Models\Model $value */
            $value = isModel($value) ? $value->getKey() : $value;
            // $value = hasTrait($this, \Spatie\Translatable\HasTranslations::class) && $this->isTranslatableAttribute($attribute) ? localeWrap($value, $locales) : $value;
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    public static function existRule($column = 'id'): string
    {
        return "exists:" . static::make()->getTable() . ',id';
    }

    public static function wogs()
    {
        return static::withoutGlobalScopes();
    }
}
