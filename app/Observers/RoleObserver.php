<?php

namespace App\Observers;

use App\Models\Role;

/**
 *
 */
class RoleObserver
{
    public function saving(Role $model)
    {
        $model->guard_name = $model->guard_name ?: getGuardForModel($model) ?: getDefaultGuardName();
    }
}
