<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

/**
 *
 */
class RoleController extends Controller
{
    /**
     * @var string|\Illuminate\Database\Eloquent\Model
     */
    public static $model = Role::class;

    /**
     * @var string|BaseJsonResource
     */
    public static $responseResource = RoleResource::class;

    /**
     * @param \App\Http\Requests\RoleRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     * @throws \Throwable
     */
    public function index(RoleRequest $request)
    {
        return $this->responseCollection(
            $this->search($request,withTrashed: $request->trashed)->paginate()
        );
    }

    /**
     * @param \App\Http\Requests\RoleRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     * @throws \Throwable
     */
    public function store(RoleRequest $request)
    {
        $validated = $request->validated();
        $validated[ 'permissions' ] = $validated[ 'permissions' ] ?? [];
        $permissions = array_pull($validated, 'permissions');

        $model = $this->createModel($request);
        if( $model && !empty($permissions) ) {
            $model->syncPermissions(...Permission::find($permissions));
        }

        return $this->responseResource($model, 201);
    }

    /**
     * @param \App\Http\Requests\RoleRequest $request
     * @param \App\Models\Role               $role
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function show(RoleRequest $request, Role $role)
    {
        return $this->responseResource($role->loadMissing([ 'permissions', 'groups', ]));
    }

    /**
     * @param \App\Http\Requests\RoleRequest $request
     * @param \App\Models\Role               $role
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function update(RoleRequest $request, Role $role)
    {
        $model = $role;
        $validated = $request->validated();
        $validated[ 'permissions' ] = $validated[ 'permissions' ] ?? null;
        $permissions = array_pull($validated, 'permissions');

        $model = $this->updateModel($request, $model);

        if( !is_null($permissions) ) {
            $model->syncPermissions(...Permission::find($permissions));
        }

        return $this->responseResource(
            $model->loadMissing([
                                    'permissions',
                                    'groups',
                                ])
        );
    }

    /**
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function destroy(RoleRequest $request, Role $role)
    {
        $aborts = [
            "User own role [{$role->name}]!" => fn() => currentUser() && currentUser()->hasRole($role->name),
        ];

        return $this->responseResource($this->deleteModel($request, $role, $aborts));
    }

    /**
     * @param \App\Models\Role $role
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function forceDestroy(RoleRequest $request, Role $role)
    {
        $aborts = [
            "User own role [{$role->name}]!" => fn() => currentUser() && currentUser()->hasRole($role->name),
        ];

        return $this->responseResource(
            $this->forceDeleteModel($request, $role, $aborts)
        );
    }

    /**
     * @param \App\Models\Role $role
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function restore(RoleRequest $request, Role $role)
    {
        return $this->responseResource($this->restoreModel($request, $role));
    }
}
