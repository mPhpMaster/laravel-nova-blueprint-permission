<?php

namespace App\Http\Resources;

use App\Http\Resources\Abstracts\AbstractResource;
use Storage;

/**
 *
 */
class UserResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var \App\Models\User $model */
        if (!($model = $this->resource)) {
            return [];
        }

        $query = $model->user_activity_logs()->where("action", "login")->latest()->first();

        return [
            // 'all_permissions' => $this->whenHasOption('all_permissions', fn() => $model->getAllPermissions()->pluck('name', 'id')->filter()->unique()),
            // 'all_roles' => $this->whenHasOption('all_roles', fn() => $model->getAllRoles()->pluck('name', 'id')->filter()->unique()),

            'id' => $model->getKey(),
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'email' => $model->email,
            // 'password' => $model->password,
            'phone' => $model->phone,
            'image' => $this->when($model->image, $model->image_url),
            'status' => $model->status,
            'position' => $model->position,
            'user_type' => $model->user_type,
            'token' => $this->fromAdditionalData('token'),
            'last_login' => $this->when($last_login = $model->user_activity_logs()->where("action", "login")->latest()->value('created_at'), fn () => $last_login->toDateTimeString()),
            // 'last_login' => $this->user_activity_logs()->count(),

            // 'is_super_admin' => $model->is_super_admin,

            'groups' => $this->whenLoaded('groups', fn () => GroupResource::collection($model->groups)),
            'roles' => $this->whenLoaded('roles', fn () => RoleResource::collection($model->roles)),
            'permissions' => $this->whenLoaded('permissions', fn () => PermissionResource::collection($model->permissions)),

            'created_at' => $this->when($model->created_at, $model->created_at),
            'updated_at' => $this->when($model->updated_at, $model->updated_at),
            'deleted_at' => $this->when($model->deleted_at, $model->deleted_at),
        ];
    }
}
