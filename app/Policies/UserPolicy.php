<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Abstracts\BasePolicy;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class UserPolicy extends BasePolicy
{
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
