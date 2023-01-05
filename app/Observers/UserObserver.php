<?php

namespace App\Observers;

use App\Events\RequestVerification;
use App\Models\User;

/**
 *
 */
class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param \App\Models\User $model
     *
     * @return void
     */
    public function created(User $model)
    {
        event(new RequestVerification($model));
    }

    /**
     * Handle the User "updated" event.
     *
     * @param \App\Models\User $model
     *
     * @return void
     */
    public function updated(User $model)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param \App\Models\User $model
     *
     * @return void
     */
    public function deleted(User $model)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param \App\Models\User $model
     *
     * @return void
     */
    public function restored(User $model)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param \App\Models\User $model
     *
     * @return void
     */
    public function forceDeleted(User $model)
    {
        //
    }
}
