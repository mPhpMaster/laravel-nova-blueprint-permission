<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\Abstracts\BasePolicy;
use App\Traits\TDenyEditAttachedPermission;
use App\Traits\TDenyEditAttachedUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class RolePolicy extends BasePolicy
{
    // use TDenyEditAttachedPermission, TDenyEditAttachedUser;

    public static string $permission_name = 'role';

    /**
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return false
     */
    public function restore(User $user, Model $model)
    {
        return false;
    }

    /**
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return false
     */
    public function forceDelete(User $user, Model $model)
    {
        return false;
    }
}
