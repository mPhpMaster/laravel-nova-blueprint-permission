<?php

namespace App\Http\Resources;

use App\Http\Resources\Abstracts\AbstractResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 */
class RoleResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $model = $this->resource;

        return [
            'id' => $model->getKey(),
            'name' => $model->name,
            // 'deleted_at' => $this->whenNotNull($model->deleted_at),
            'deleted_at' => $this->when($model->deleted_at, $model->deleted_at),

            'groups' => $this->whenLoadedAndNotEmpty('groups', fn() => GroupResource::collection($model->groups)),
            // 'roles' => $this->whenLoaded('roles', fn() => RoleResource::collection($model->roles), []),
            'permissions' => $this->whenLoadedAndNotEmpty('permissions', fn() => PermissionResource::collection($model->permissions)),
            // 'all_permissions' => $model->getAllPermissions()->map->name->filter()->unique(),
            // 'all_roles' => $model->getAllRoles()->map->name->filter()->unique(),
            // 'all_permissions' => PermissionResource::collection($model->getAllPermissions()),
        ];
    }
}
