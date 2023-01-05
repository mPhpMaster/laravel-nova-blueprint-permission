<?php

namespace App\Http\Resources;

use App\Http\Resources\Abstracts\AbstractResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 */
class PermissionResource extends AbstractResource
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
        /** @var \App\Models\Permission $model */
        $model = $this->resource;

        return [
            'id' => $model->getKey(),
            'name' => $model->name,
            'assigned_to_roles' => $this->whenLoaded('roles',fn()=>$model->roles),
            'deleted_at' => $this->whenNotNull($model->deleted_at, $model->deleted_at),
        ];
    }
}
