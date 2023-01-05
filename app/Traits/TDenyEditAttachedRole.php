<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\User;
use App\Models\Abstracts\Model;

/**
 * @mixin \App\Policies\Abstracts\BasePolicy
 */
trait TDenyEditAttachedRole
{
    /**
     * Hides the edit relation button
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Abstracts\Model $permission
     * @param \App\Models\Role  $role
     *
     * @return bool
     */
    public function attachRole(User $user, Model $permission, Role $role)
    {
        return $this->canAttach($user, $permission, $role);
    }
}
