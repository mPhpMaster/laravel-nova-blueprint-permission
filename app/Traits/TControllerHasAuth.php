<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * @mixin \App\Interfaces\IControllerHasAuth
 */
trait TControllerHasAuth
{
    /**
     * @var string|null
     */
    public static ?string $permission_name = null;

    /**
     * @var string|\Illuminate\Database\Eloquent\Model
     */
    public static $model;

    /** @var array|string[] */
    public static array $abilities_map = [
        // 'method' => 'policy method'
        'index' => 'viewAny',

        'show' => 'view',

        'create' => 'create',
        'store' => 'create',

        'update' => 'update',
        'edit' => 'update',

        'destroy' => 'delete',
        'forceDestroy' => 'forceDelete',

        'restore' => 'restore',
    ];

    /** @var array|string[] */
    public static array $methods_without_models = [
        'index',
        'create',
        'store',
    ];

    /** @return string|null */
    public static function getPermissionName(): ?string
    {
        return static::$permission_name ?? snake_case(str_singular(str_before_last_count(class_basename(static::class), 'Controller')));
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array of ['method' => 'policy method']
     */
    public static function getAbilitiesMap(): array
    {
        return static::$abilities_map ?? [];
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    public static function getMethodsWithoutModels(): array
    {
        return static::$methods_without_models ?? [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|string
     */
    public static function getModelClass()
    {
        $model = static::$model ?? studly_case(str_before_last_count(class_basename(static::class), "Controller"));
        abort_if(!$model, 404, static::class . "::\$model Not Found!");

        if( $model ) {
            $_model = class_exists($model) ? $model : null;
            $_model = class_exists($__model = "\\App\\Models\\{$model}") ? $__model : $_model;
            $model = $_model ?? User::class;
        }

        return $model;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|string|null $model
     *
     * @return array
     */
    public static function getModelAndAbility(Model|string|null $model = null): array
    {
        $model ??= static::getPermissionName();
        // duE([static::class=>[$model,static::$permission_name]]);
        $_model = $model;
        /** @var object $model */
        /** @var object $model */
        if( $model = (is_string($model) ? $model : class_basename(get_class($model))) ) {
            if( str_contains($model, "\\") || class_exists($model) ) {
                $model = snake_case(str_singular(class_basename($model)));
            }
        }

        try {
            /** @var string $model */
            $method_name = isRunningInConsole() && dd(__LINE__, $model, $_model) || camel_case(currentActionName());
        } catch(\Exception $exception) {
            $method_name = null;
        }

        $ability = $method_name ? (static::getAbilitiesMap()[ $method_name ] ?? $method_name) : static::getModelClass();

        return [
            $model ?? static::getModelClass(),
            $method_name,
            $ability,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function makeModel(): Model
    {
        abort_if(!($model = static::getModelClass()), 404, static::class . "::\$model Not Found!");

        if( $id = currentModel() ) {
            return $model::findOrFail($id);
        }

        if( $id = request()->offsetGet($class = str_singular(snake_case(class_basename($model)))) ) {
            return $model::findOrFail($id);
        }

        abort(404, static::class . "::model() Not Found!");
    }

    /**
     * @return void
     */
    public function authorizeCurrentResource()
    {
        [ $model, $method, $permissionAbility ] = static::getModelAndAbility();
        $model ??= static::getModelClass();
        $model = in_array($method, $this->resourceMethodsWithoutModels()) ?
            "\\App\\Models\\" . studly_case(str_singular($model)) :
            $model;

        if( !isRunningInConsole() && ($model && $method) ) {
            // dd($method,$permissionAbility,$model);
            $modelName = (class_exists($model) || stringContains($model, "\\")) ?
                snake_case(str_singular(class_basename($model))) : $model;
            // duE([__LINE__=>[
            //         'permission'=>$method,
            //         'model'=>$model,
            //         'modelN'=>$modelName,
            //         'permissionAbility'=>$permissionAbility,
            //     ]]
            // );

            $this->authorize($permissionAbility, $model);
            // $this->authorizeResource($model, 'role.delete');
            // [ $permission_model, $permission_permission ] = explode('.', $permission);

            $this->authorizeResource($model, $permissionAbility);
            // dd($model, $permissionAbility,$method);
            // $this->authorizeResource($model, $method);
        }
    }
    /**
     * @return mixed|string|null
     */
    public function getRouteName()
    {
        return currentRoute()?->getName() ??
            Route::current()?->getName() ??
            request()->route()?->getAction('as') ??
            null;
    }

    /**
     * @return mixed|string|null
     */
    public function fixRouteName(?string $routeName = null)
    {
        $routeName ??= static::getRouteName();
        $names = explode('.', $routeName);
        $names[ 0 ] = str_singular(snake_case(trim($names[ 0 ] ?? "")));

        return implode('.', $names);
    }
}
