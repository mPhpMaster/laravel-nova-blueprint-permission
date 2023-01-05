<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Interfaces\IImage;
use Illuminate\Validation\Rule;
use Illuminate\Database\Schema\Builder;
use App\Http\Requests\Abstracts\AbstractFormRequest;

/**
 *
 */
class UserRequest extends AbstractFormRequest
{
    public static ?string $permission_name = 'user';
    public static ?string $route_model_parameter_name = 'user';

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
            'first_name' => [ 'required', 'string', 'max:' . Builder::$defaultStringLength ],
            'last_name' => [ 'required', 'string', 'max:' . Builder::$defaultStringLength ],
            'email' => [ "required", 'email', "unique:users,email" ],
            'password' => [ 'required', 'string', 'min:8', 'confirmed' ],
            'phone' => [ 'numeric' ],
            'position' => [ 'string' ],
            'status' => 'integer',
            'image' => IImage::rules,
            'user_type' => ['string',  Rule::in(User::getAllUserTypes())],
            'roles' => [ 'array' ],
            'roles.*' => [ 'numeric' ],
            'groups' => [ 'array' ],
            'groups.*' => [ 'numeric' ],
        ];
    }

    public function updateRules(string $type): array
    {
         return [
            'first_name' => [ 'required', 'string', 'max:' . Builder::$defaultStringLength ],
            'last_name' => [ 'required', 'string', 'max:' . Builder::$defaultStringLength ],
            'email' => [ "required", 'email', "unique:users,email,{$this->modelId()}" ],
            'password' => [ 'string', 'min:8' ],
            'phone' => [ 'numeric' ],
            'status' => 'integer',
            'position' => [ 'string' ],
            'image' => IImage::rules,
            'user_type' => [ 'required', 'string',  Rule::in(User::getAllUserTypes())],
            'roles' => [ 'array' ],
            'roles.*' => [ 'numeric' ],
            'groups' => [ 'array' ],
            'groups.*' => [ 'numeric' ],
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
