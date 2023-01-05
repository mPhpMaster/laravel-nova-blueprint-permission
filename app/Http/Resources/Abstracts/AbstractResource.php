<?php

namespace App\Http\Resources\Abstracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 *
 */
class AbstractResource extends JsonResource
{
    /**
     * The resource instance.
     *
     * @var \App\Models\Abstracts\Model
     */
    public $resource;

    protected array $options = [];

    protected ?int $status_code = null;

    /**
     * The additional data that should be added to the top-level resource array.
     *
     * @var array
     */
    public $with = [
        'success' => null,
        'message' => null,
    ];

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->success();
    }

    /**
     * @param mixed       $resource
     * @param string|null $message
     * @param int|null    $status_code
     *
     * @return $this
     */
    public static function makeSuccess($resource, ?string $message = null, ?int $status_code = null): static
    {
        return static::make($resource)
                     ->success(true)
                     ->messageWhenNotNull($message)
                     ->setStatusCode($status_code);
    }

    /**
     * @param mixed       $resource
     * @param string|null $message
     * @param int|null    $status_code
     *
     * @return $this
     */
    public static function makeError($resource, ?string $message = null, ?int $status_code = null): static
    {
        return static::make($resource)
                     ->error(true)
                     ->messageWhenNotNull($message)
                     ->setStatusCode($status_code ?? 422);
    }

    /**
     * @param bool        $success
     * @param string|null $message
     *
     * @return $this
     */
    public function success(bool $success = true, ?string $message = null, ?int $status_code = null): static
    {
        $this->with[ 'success' ] = $success;
        if( !is_null($message) ) {
            $this->message($message);
        } else {
            $this->message($success ? __('messages.success') : __('messages.error'), false);
        }

        $this->setStatusCode($status_code ?? $this->calculateStatus());

        return $this;
    }

    /**
     * @param bool        $error
     * @param string|null $message
     * @param int|null    $status_code
     *
     * @return $this
     */
    public function error(bool $error = true, ?string $message = null, ?int $status_code = null): static
    {
        $this->with[ 'success' ] = !$error;
        if( !is_null($message) ) {
            $this->message($message);
        } else {
            $this->message(!$error ? __('messages.success') : __('messages.error'));
        }

        $this->setStatusCode($status_code ?? 422);

        return $this;
    }

    /**
     * @param string|null $message
     * @param bool        $force
     *
     * @return $this
     */
    public function message(?string $message = null, bool $force = true): static
    {
        if( $force || !isset($this->with[ 'message' ]) || is_null($this->with[ 'message' ]) ) {
            $this->with[ 'message' ] = $message;
        }

        return $this;
    }

    /**
     * @param string|null $message
     * @param bool        $force
     *
     * @return $this
     */
    public function messageWhenNotNull(?string $message = null, bool $force = true): static
    {
        if( !is_null($message) ) {
            return $this->message($message, $force);
        }

        return $this;
    }

    /**
     * Calculate the appropriate status code for the response.
     *
     * @return int
     */
    protected function calculateStatus()
    {
        return $this->resource instanceof Model &&
        $this->resource->wasRecentlyCreated ? 201 : 200;
    }

    /**
     * Options.
     *
     * @param array|string|mixed $key
     * @param mixed|null         $value
     *
     * @return $this
     */
    public function options($key, mixed $value = true): static
    {
        if( !is_array($key) ) {
            $this->options[ $key ] = $value;
        } else {
            $this->options = $key;
        }

        return $this;
    }

    /**
     * Options.
     *
     * @param array|string $key
     *
     * @return $this
     */
    public function trueOptions(...$key): static
    {
        $key = count($key) === 1 && is_array(head($key)) ? head($key) : $key;
        $key = \Illuminate\Support\Arr::isAssoc($key) ? array_keys($key) : $key;
        foreach( $key as $_key ) {
            $this->options[ $_key ] = true;
        }

        return $this;
    }

    /**
     * Options.
     *
     * @param array|string $key
     *
     * @return $this
     */
    public function falseOptions(...$key): static
    {
        $key = count($key) === 1 && is_array(head($key)) ? head($key) : $key;
        $key = \Illuminate\Support\Arr::isAssoc($key) ? array_keys($key) : $key;
        foreach( $key as $_key ) {
            $this->options[ $_key ] = false;
        }

        return $this;
    }

    /**
     * @param string|null $key
     *
     * @return bool
     */
    public function hasOption(?string $key = null): bool
    {
        return is_null($key) ? !empty($this->options) : isset($this->options[ $key ]);
    }

    /**
     * Add additional meta data to the resource response.
     *
     * @param array $data
     *
     * @return $this
     */
    public function additionalData(array $data): static
    {
        $this->additional[ static::$wrap ] = $this->additional[ static::$wrap ] ?? [];
        $this->additional = array_merge($this->additional, [ static::$wrap => $data ]);

        return $this;
    }

    /**
     * Customize the response for a request.
     *
     * @param \Illuminate\Http\Request      $request
     * @param \Illuminate\Http\JsonResponse $response
     *
     * @return void
     */
    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->status_code);
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setStatusCode(?int $code = null): static
    {
        $this->status_code = $code ?? $this->calculateStatus();

        return $this;
    }

    /**
     * @param $key
     * @param $default
     *
     * @return array|\Illuminate\Http\Resources\MissingValue|mixed
     */
    public function fromAdditionalData($key = null, $default = MissingValue::class): mixed
    {
        $default = ($default = value($default)) === MissingValue::class ? new $default : $default;
        $key = value($key) ?: null;

        $result = $this->additional[ static::$wrap ] = $this->additional[ static::$wrap ] ?? [];
        if( is_null($key) ) {
            return empty($result) ? new MissingValue() : $result;
        }

        return array_pull(
            $this->additional[ static::$wrap ],
            $key,
            $default
        );
    }

    /**
     * @param $key
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function fromAdditionalDataWhenNotEmpty($key): mixed
    {
        $key = value($key) ?: null;

        $result = $this->additional[ static::$wrap ] = $this->additional[ static::$wrap ] ?? null;
        if( !is_null($key) ) {
            $result = array_pull($this->additional[ static::$wrap ], $key);
        }

        return empty($result) ? new MissingValue() : $result;
    }

    /**
     * @param $key
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    public function fromAdditionalDataWhenNotNull($key): mixed
    {
        $key = value($key) ?: null;

        $result = $this->additional[ static::$wrap ] = $this->additional[ static::$wrap ] ?? null;
        if( !is_null($key) ) {
            $result = array_pull($this->additional[ static::$wrap ], $key);
        }

        return is_null($result) ? new MissingValue() : $result;
    }

    /**
     * @param string $option
     * @param        $value
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenOption(string $option, $optionValue = null, $value = null): mixed
    {
        $_value = isClosure($value) ? $value : fn() => $value;
        $condition = $this->hasOption($option) && $this->options[ $option ] === $optionValue;

        return $this->when($condition, $_value);
    }

    /**
     * @param string $option
     * @param        $value
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenTrueOption(string $option, $value = null): mixed
    {
        $_value = isClosure($value) ? $value : fn() => $value;
        $condition = $this->hasOption($option) && ! !$this->options[ $option ];

        return $this->when($condition, $_value);
    }

    /**
     * @param string $option
     * @param        $value
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenFalseOption(string $option, $value = null): mixed
    {
        $_value = isClosure($value) ? $value : fn() => $value;
        $condition = $this->hasOption($option) && !$this->options[ $option ];

        return $this->when($condition, $_value);
    }

    /**
     * @param string $option
     * @param        $value
     * @param        $default
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenHasOption(string $option, $value = null, $default = null): mixed
    {
        if( func_num_args() < 3 ) {
            $default = new MissingValue;
        }
        $_value = isClosure($value) ? $value : fn() => $value;
        $condition = $this->hasOption($option);

        return $this->when($condition, $_value, $default);
    }

    /**
     * @param $relationship
     * @param $value
     * @param $default
     *
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function whenLoadedAndNotEmpty($relationship, $value = null, $default = null): mixed
    {
        if( func_num_args() < 3 ) {
            $default = new MissingValue;
        }
        $relation = null;
        $_value = isClosure($value) ? $value : fn() => $value;
        $condition = $this->resource->relationLoaded($relationship) && toCollect($relation = $this->resource->getRelation($relationship))->count();

        return $this->when($condition && !empty($relation), $_value, $default);
    }
}
