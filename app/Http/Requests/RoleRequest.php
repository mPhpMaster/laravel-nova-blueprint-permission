<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\AbstractFormRequest;

/**
 *
 */
class RoleRequest extends AbstractFormRequest
{
    public static ?string $permission_name = 'role';
    public static ?string $route_model_parameter_name = 'role';

    public function indexRules(string $type): array
    {
        return [];
    }

    public function showRules(string $type): array
    {
        return [];
    }

    public function storeRules(string $type): array
    {
        return [
            'name' => 'required|unique:roles|max:32',
            'permissions' => 'array',
            'permissions.*' => 'required|integer',
        ];
    }

    public function updateRules(string $type): array
    {
        return [
            'name' => "required|unique:roles,name,{$this->modelId()}",
            'permissions' => 'array',
            'permissions.*' => 'required|integer',
        ];
    }

    public function destroyRules(string $type): array
    {
        return [];
    }

    public function forceDestroyRules(string $type): array
    {
        return [];
    }

    public function restoreRules(string $type): array
    {
        return [];
    }
}
