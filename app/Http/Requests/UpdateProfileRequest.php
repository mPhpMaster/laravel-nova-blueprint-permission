<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\AbstractFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 *
 */
class UpdateProfileRequest extends AbstractFormRequest
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
        'update_profile' => 'edit',
    ];

    public function indexRules(string $type): array
    {
        return [];
    }

    public function showRules(string $type): array
    {
        return [];
    }

    public function updateProfileRules(string $type): array
    {
        return [
            // 'email' => [ Rule::unique('users')->ignore(currentUser()->id) ],
            'last_name' => [ 'required', 'string' ],
            'first_name' => [ 'required', 'string' ],
            'phone' => ['numeric'],
            // 'position' => ['string' ],
            // 'user_type' => ['string',  Rule::in(User::getAllUserTypes())],
        ];
    }

    public function storeRules(string $type): array
    {
        return [];
    }

    public function updateRules(string $type): array
    {
        return $this->updateProfileRules($type);
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
