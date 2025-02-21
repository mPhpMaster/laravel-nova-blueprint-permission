<?php

namespace App\Traits;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait TModelTranslation
{
    /**
     * Returns translations file name.
     *
     * @return string|null
     */
    public static function getTranslationKey(): ?string
    {
        return str_singular(snake_case(static::make()->getTable()));
    }

    /**
     * Encode the given value as JSON.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
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
     */
    public static function trans($key = null, $replace = [], $locale = null, $default = null)
    {
        $transKey = static::getTranslationKey() ?? static::make()->getTable();
        $models = [
            str_singular((snake_case($transKey))),
            str_plural((snake_case($transKey))),

            str_singular((snake_case(class_basename(static::class)))),
            str_plural((snake_case(class_basename(static::class)))),
        ];

        $replace = !is_array($replace = value($replace)) ? array_wrap($replace) : $replace;
        $default = is_null($default = value($default)) ? $key : $default;

        $result = null;
        foreach( $models as $model ) {
            if( $result = getTrans(
                "models/{$model}.{$key}",
                getTrans(
                    "models/{$model}.fields.{$key}",
                    null,

                    $replace,
                    $locale
                ),
                $replace,
                $locale
            ) ) {
                break;
            }
        }
        $result ??= value($default);

        if( $result === $key ) {
            $result = $result === 'plural' ? str_plural($model) : ($result === 'singular' ? str_singular($model) : $result);
            $result = \Str::headline($result);
        }

        return $result;
    }

    public static function fieldTrans($key = null, $replace = [], $locale = null, $default = null)
    {
        $trans = collect(static::trans('fields', $replace, $locale, $default));
        $trans->each(function($v, $k) use ($trans) {
            $k = str($k);
            foreach( array_filter([
                                      $k->toString(),
                                      $k->camel()->toString(),
                                      $k->snake()->toString(),
                                      $k->snake()->upper()->toString(),
                                      $k->snake()->lower()->toString(),
                                      $k->studly()->toString(),
                                      $k->title()->toString(),
                                      $k->headline()->toString(),
                                      $k->slug()->toString(),
                                      $k->snake('-')->toString(),
                                      $k->snake('-')->upper()->toString(),
                                      $k->snake('-')->lower()->toString(),
                                  ]) as $item ) {
                $trans->put($item, $v);
            }
        });
        $default = is_null($default) ? static::trans(...func_get_args()) : $default;

        return $trans->has($key) ? $trans->get($key) : value($default);
    }

    public static function generateValidationRequiredMessages(array $validations): array
    {
        return generateValidationRequiredMessages($validations, [ static::class, 'fieldTrans' ]);
    }
}
