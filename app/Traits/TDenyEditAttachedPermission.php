<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \App\Policies\Abstracts\BasePolicy
 */
trait TDenyEditAttachedPermission
{
    /**
     * Hides the edit relation button
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Role       $role
     * @param \App\Models\Permission $permission
     *
     * @return bool
     */
    public function attachPermission(User $user, Model $role, Permission $permission)
    {
        return $this->canAttach($user, $role, $permission);
    }
}
