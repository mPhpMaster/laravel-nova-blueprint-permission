<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\ResponseUtil;
use App\Http\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Nova\Query\ApplySoftDeleteConstraint;
use Laravel\Nova\TrashedStatus;
use \Response;

/**
 *
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var string|\Illuminate\Http\Resources\Json\JsonResource
     */
    public static $responseResource = Resource::class;

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

    public function __construct()
    {
        // dd(
        // currentControllerCalass()
        // getControllerPermissionPrefix(/*$this, static::$permission_name*/)
        // );
        //     static::getRouteName(),
        //     static::fixRouteName(),
        // );
        // dd(
        //     __LINE__,
        //     request()->route('index')
        // );
        // dE(static::class,static::$model, static::$permission_name);
        // $this->authorizeResource(static::$model, static::$permission_name);

        // [ $model, $method, $permissionAbility ] = static::getModelAndAbility();
        // dd($permissionAbility, snake_case(str_singular(class_basename($model))));
        // dd(static::$model, snake_case(str_singular(class_basename($model))));
        // $this->authorizeResource(static::$model, snake_case(str_singular(class_basename($model))));
        // dd(/);
        // $this->authorizeCurrentResource();
        // duE($this::resourceAbilityMap());
    }

    /**
     * @param \Illuminate\Http\Request                   $request
     * @param string                                     $requestKey
     * @param string                                     $columnName
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Throwable
     */
    public function search(Request $request, string $columnName = 'name', string $requestKey = 'search', ?Builder $query = null, array $aborts = [], $withTrashed = TrashedStatus::DEFAULT): Builder
    {
        $this->aborts($aborts);
        $trashedKey = 'trashed';
        $query ??= $this->query();
        $query = (new ApplySoftDeleteConstraint)->__invoke($query, trim($withTrashed ?? $request->get($trashedKey)));

        if( $search = $request->get($requestKey, '') ) {
            return $query->where($columnName, 'like', "%{$search}%");
        }

        return $query;
    }

    /**
     * @param \Illuminate\Http\Request                   $request
     * @param \Illuminate\Database\Eloquent\Builder|null $query
     * @param array                                      $aborts
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function createModel(Request $request, ?Builder $query = null, array $aborts = []): Builder|Model
    {
        $this->aborts($aborts);
        $query ??= $this->query();

        return $query->create($request->validated());
    }

    /**
     * @param \Illuminate\Http\Request                                                  $request
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $query
     * @param array                                                                     $aborts
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function updateModel(Request $request, Builder|Model $query, array $aborts = []): Builder|Model
    {
        $this->aborts($aborts);

        $query->update($request->validated());
        return $query;
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $aborts
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function deleteModel(Request $request, Model $model, array $aborts = []): Model
    {
        $this->aborts($aborts);

        $request->validated();
        $model && $model->delete();

        return $model;
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function forceDeleteModel(Request $request, Model $model, array $aborts = []): Model
    {
        $this->aborts($aborts);

        $request->validated();
        $model && $model->forceDelete();

        return $model;
    }

    /**
     * @param \Illuminate\Http\Request            $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $aborts
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function restoreModel(Request $request, Model $model, array $aborts = []): Model
    {
        $this->aborts($aborts);

        $request->validated();
        $model->trashed() && $model->restore();

        return $model;
    }

    /**
     * @param \Closure|null $callback
     *
     * @return string|\Illuminate\Http\Resources\Json\JsonResource|null
     */
    public static function getResponseResource(?\Closure $callback = null)
    {
        $callback ??= fn($m) => $m;
        $model = static::$responseResource ?? studly_case(str_before_last_count(class_basename(static::class), "Controller"));

        if( $model ) {
            if( str($model)->is(static::$responseResource) ) {
                $model = class_exists($model) ? $model : null;
            } else {
                $model = $model === "Auth" ? "User" : $model;
                $modelResource = "\\App\\Http\\Resources\\{$model}Resource";
                $model = class_exists($modelResource) ? $modelResource : null;
            }
        }

        return $callback($model ?? Resource::class);
    }

    /**
     * Register middleware on the controller.
     *
     * @param \Closure|array|string $middleware
     * @param array                 $options
     *
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        // foreach( (array) $middleware as $m ) {
        //     logger("    middleware {$m} registerd");
        // }

        return parent::middleware($middleware, $options);
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return static::$abilities_map ?? [];
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return static::$methods_without_models ?? [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Throwable
     */
    public function query()
    {
        throw_if(!static::$model, "[" . static::class . "] Model Not Exists!");

        return static::$model::query();
    }

    /**
     * @param mixed $data
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string|null
     */
    public function responseResource($data, ?int $code = 200)
    {
        return
            $this->sendResponse(
                static::getResponseResource(fn(string|BaseJsonResource|null $resource) => $resource::make($data)),
                null,
                $code
            );
    }

    /**
     * @param mixed $data
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string|null
     */
    public function responseCollection($data)
    {
        return $this->sendResponseCollection(
            static::getResponseResource(fn(string|BaseJsonResource|null $resource) => $resource::collection($data))
        );
    }

    /**
     * @param \Illuminate\Http\Resources\Json\JsonResource $result
     * @param string|null                                  $message
     * @param int                                          $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponseCollection(\Illuminate\Http\Resources\Json\JsonResource $result, $message = null, int $code = 200)
    {
        return ResponseUtil::makeResponseCollection($message ?? __('messages.success'), $result)
                           ->toResponse(request())
                           ->setStatusCode($code);
    }

    /**
     * @param      $result
     * @param null $message
     * @param int  $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message = null, int $code = 200): \Illuminate\Http\JsonResponse
    {
        return Response::json(ResponseUtil::makeResponse($message ?? __('messages.success'), $result), $code);
    }

    /**
     * @param      $result
     * @param null $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCreated($result, $message = null): \Illuminate\Http\JsonResponse
    {
        return $this->sendResponse($result, $message, 201);
    }

    /**
     * @param     $error
     * @param int $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, int $code = 404): \Illuminate\Http\JsonResponse
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    /**
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccess($data = null, $message = null, int $code = 200): \Illuminate\Http\JsonResponse
    {
        return Response::json(ResponseUtil::makeResponse($message ?? __('messages.success'), $data), $code);
    }

    public function aborts(array $aborts)
    {
        foreach( $aborts as $message => $condition ) {
            abort_if(value($condition), 403, $message);
        }
    }
}
