<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

/**
 *
 */
class PermissionController extends Controller
{
    /**
     * @var string|\Illuminate\Database\Eloquent\Model
     */
    public static $model = Permission::class;

    /**
     * @var string|BaseJsonResource
     */
    public static $responseResource = PermissionResource::class;

    /**
     * @param \App\Http\Requests\PermissionRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     * @throws \Throwable
     */
    public function index(PermissionRequest $request)
    {
        return $this->responseCollection(
            $this->search($request,withTrashed: $request->trashed)->paginate()
        );
    }

    /**
     * @param \App\Http\Requests\PermissionRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     * @throws \Throwable
     */
    public function store(PermissionRequest $request)
    {
        return $this->responseResource($this->createModel($request), 201);
    }

    /**
     * @param \App\Http\Requests\PermissionRequest $request
     * @param \App\Models\Permission               $permission
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function show(PermissionRequest $request, Permission $permission)
    {
        return $this->responseResource($permission);
    }

    /**
     * @param \App\Http\Requests\PermissionRequest $request
     * @param \App\Models\Permission               $permission
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        return $this->responseResource($this->updateModel($request, $permission));
    }

    /**
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function destroy(PermissionRequest $request, Permission $permission)
    {
        $aborts = [
            "User own permission [{$permission->name}]!" => fn() => currentUser() && currentUser()->hasPermission($permission->name),
        ];

        return $this->responseResource($this->deleteModel($request, $permission, $aborts));
    }

    /**
     * @param \App\Models\Permission $permission
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function forceDestroy(PermissionRequest $request, Permission $permission)
    {
        $aborts = [
            "User own permission [{$permission->name}]!" => fn() => currentUser() && currentUser()->hasPermission($permission->name),
        ];

        return $this->responseResource(
            $this->forceDeleteModel($request, $permission, $aborts)
        );
    }

    /**
     * @param \App\Models\Permission $permission
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function restore(PermissionRequest $request, Permission $permission)
    {
        return $this->responseResource($this->restoreModel($request, $permission));
    }
}
