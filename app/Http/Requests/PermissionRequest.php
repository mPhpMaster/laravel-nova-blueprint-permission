<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\AbstractFormRequest;

/**
 *
 */
class PermissionRequest extends AbstractFormRequest
{
    public static ?string $permission_name = 'permission';
    public static ?string $route_model_parameter_name = 'permission';

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
            'name' => 'required|unique:permissions|max:32',
        ];
    }

    public function updateRules(string $type): array
    {
        return [
            'name' => "required|unique:permissions,name,{$this->modelId()}",
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
