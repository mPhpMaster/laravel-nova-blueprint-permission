<?php

namespace App\Observers;

use App\Models\Permission;

/**
 *
 */
class PermissionObserver
{
    public function saving(Permission $model)
    {
        $model->guard_name = $model->guard_name ?: getDefaultGuardName();
    }
}
