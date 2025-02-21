<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Abstracts\BasePolicy;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class UserProfilePolicy extends BasePolicy
{

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function viewAny(?Authenticatable $user = null)
    {
        return false;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function index(?Authenticatable $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User|null               $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function view(?User $user, Model $model)
    {
        return false;
    }

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

    public function update(User $user, Model $model)
    {
        if( $user->id === $model->id )
        {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function delete(User $user, Model $model)
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
