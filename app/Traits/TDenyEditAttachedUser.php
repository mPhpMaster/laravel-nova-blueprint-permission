<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Abstracts\Model;

/**
 * @mixin \App\Policies\Abstracts\BasePolicy
 */
trait TDenyEditAttachedUser
{
    /**
     * Hides the edit relation button
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Abstracts\Model  $role
     * @param \App\Models\User $moodel
     *
     * @return bool
     */
    public function attachUser(User $user, Model $role, User $moodel)
    {
        return $this->canAttach($user, $role, $moodel);
    }
}
