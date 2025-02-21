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
    public static string $permission_name = 'User';

}
