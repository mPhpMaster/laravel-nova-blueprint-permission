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
    public static string $permission_name = 'Role';

}
