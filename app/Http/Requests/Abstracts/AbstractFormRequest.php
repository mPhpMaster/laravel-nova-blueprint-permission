<?php

namespace App\Http\Requests\Abstracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 *
 */
abstract class AbstractFormRequest extends FormRequest
{
    /**
     * @var string|null
     */
    public static ?string $permission_name = null;
    /**
     * @var string|null
     */
    public static ?string $route_model_parameter_name = null;
    /**
     * @var string|null
     */
    public static ?string $route_model = null;

    /** @var array|string[] */
    public static array $names_map = [
        // 'method' => 'policy method'
        // 'index' => 'view_Any',
        'show' => 'view',
        'store' => 'create',
        'update' => 'edit',
        'destroy' => 'delete',
        'force_destroy' => 'force_delete',
    ];

    protected function guessPermissionName(string $delimiter = '_'): ?string
    {
        $name = class_basename(static::class);
        $name = str_before_last_count($name, 'FormRequest');
        $name = str_before_last_count($name, 'Request');
        $name = str_singular(snake_case($name, '-'));
        if( $permission_name = static::$permission_name ?? null ) {
            $name = starts_with($name, $permission_name) ? str_after($name, $permission_name) : $name;
        }
        $name = $name ?: null;
        $name = count(explode('-', $name)) < 2 ? null : $name;
        $name ??= guessPermissionName(static::$permission_name ?? $this->getControllerClass(), $this->route()->getActionMethod(), $delimiter);
        $name = snake_case($name, $delimiter);
        if( (static::$permission_name ?? null) ) {
            $name = str_start($name, static::$permission_name . $delimiter);
        }

        return toCollect(explode($delimiter, $name))
            ->map([ static::class, 'fixMethodName' ])
            ->implode($delimiter);
    }

    public static function fixMethodName(?string $name, int $index): ?string
    {
        if( $name ) {
            foreach( static::$names_map as $search => $replace ) {
                if( snake_case($name) === snake_case($search) ) {
                    $name = $replace;
                    break;
                }
            }
        }

        return $name;
    }

    public function getPermissionName(mixed $default = null): ?string
    {
        $permission_name = $this->guessPermissionName('.');
        $permission_name = $permission_name ?: guessPermissionName(static::$permission_name ?? $this->getControllerClass(), $this->getControllerMethod());

        return $permission_name ?: value($default);
    }

    public function getRouteModelParameterName(mixed $default = null): ?string
    {
        $requestName = $this->guessPermissionName($delimiter = '_');
        if( !($name = static::$route_model_parameter_name ?? null) ) {
            $name = replaceAll(
                array_merge(
                    [ "update" => "", "edit" => "" ],
                    [ "create" => "", "store" => "" ],
                    [ "delete" => "", "destroy" => "" ],
                    [ "index" => "", "view_any" => "", "list" => "" ],
                    [ "show" => "", "view" => "" ],
                ),
                $requestName
            );

            $name = snake_case(replaceAll([ "{$delimiter}{$delimiter}" => "{$delimiter}", ], $name), $delimiter);
        }

        return trim($name ?: value($default), $delimiter);
    }

    public function getRouteModelClass(mixed $default = null): ?string
    {
        if( !($model = static::$route_model ?? null) ) {
            $model = studly_case($this->getRouteModelParameterName('_'));
            if( !class_exists($model) ) {
                if( !class_exists($_model = "\\App\\Models\\{$model}") ) {
                    try {
                        $model = get_class(app($model));
                    } catch(\Exception $exception) {
                        $model = null;
                    }
                } else {
                    $model = $_model;
                }
            }
        }

        $model = $model ?: currentModelViaControllerName($this->getControllerClass());

        return $model ?: value($default);
    }

    public function model(?string $parameterName = null): Model|string|null
    {
        return $this->route()->parameter($parameterName ?: $this->getRouteModelParameterName());
    }

    public function modelId(?string $parameterName = null): ?string
    {
        return $this->route()->originalParameter($parameterName ?: $this->getRouteModelParameterName());
    }

    public function getControllerClass(): ?string
    {
        return $this->route()->getControllerClass();
    }

    public function getControllerMethod(): ?string
    {
        $name = $this->route()->getActionMethod();

        return static::fixMethodName($name, 0);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $permission = $this->getPermissionName();

        if( !isPermissionExists($permission) ) {
            $result = true;
        } elseif( $permission ) {
            $result = currentUser()->hasPermissionTo($permission);
            logger(str_after(static::class, 'App\Http\Requests\\') . " [$permission] (" . ($result ? 'TRUE' : 'FALSE') . ")");
        } else {
            $result = true;
            logger(str_after(static::class, 'App\Http\Requests\\') . " [NO PERMISSION] (" . ($result ? 'TRUE' : 'FALSE') . ")");
        }

        return $result;
    }

    public function rules(): array
    {
        try {
            $requestName = $this->getControllerMethod();
        } catch(\Exception $exception) {
            $requestName = $this->guessPermissionName($delimiter = '_');
            $requestName = camel_case(str_after($requestName, $delimiter));
        }

        return match (camel_case($requestName)) {
            'index' => $this->indexRules($requestName),
            'store', 'create' => $this->storeRules($requestName),
            'update', 'edit' => $this->updateRules($requestName),
            'delete', 'destroy' => $this->destroyRules($requestName),
            'forceDestroy', 'forceDelete' => $this->forceDestroyRules($requestName),
            'restore' => $this->restoreRules($requestName),
            default => method_exists($this, $methodName = "{$requestName}Rules") ? $this->$methodName($requestName) : $this->showRules($requestName)
        };
    }

    abstract public function indexRules(string $type): array;

    abstract public function showRules(string $type): array;

    abstract public function storeRules(string $type): array;

    abstract public function updateRules(string $type): array;

    abstract public function destroyRules(string $type): array;

    abstract public function forceDestroyRules(string $type): array;

    abstract public function restoreRules(string $type): array;
}
