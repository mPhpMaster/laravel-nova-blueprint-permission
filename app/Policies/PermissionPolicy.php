<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Abstracts\BasePolicy;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class PermissionPolicy extends BasePolicy
{
    public static string $permission_name = 'Permission';
    public static bool $super_admin_only = true;


    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function update(User $user, Model $model)
    {
        return false;
    }

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
