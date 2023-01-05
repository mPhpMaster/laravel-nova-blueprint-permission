<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\AbstractFormRequest;
use Illuminate\Validation\Rule;

/**
 *
 */
class ChangePasswordRequest extends AbstractFormRequest
{
    public static ?string $permission_name = 'user';
    public static array $names_map = [
        // 'method' => 'policy method'
        // 'index' => 'view_Any',
        'show' => 'view',
        'store' => 'create',
        'update' => 'edit',
        'destroy' => 'delete',
        'force_destroy' => 'force_delete',
        'change_password' => 'edit',
    ];

    public function indexRules(string $type): array
    {
        return [];
    }

    public function showRules(string $type): array
    {
        return [];
    }

    public function changePasswordRules(string $type): array
    {
        return [
            'email' => [ "required", 'email', Rule::in(currentUser()->email) ],
            'password' => [ 'required', 'string', 'min:8', 'current_password' ],
            'new_password' => [ 'required', 'string', 'min:8' ],
        ];
    }

    public function storeRules(string $type): array
    {
        return [];
    }

    public function updateRules(string $type): array
    {
        return $this->changePasswordRules($type);
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
