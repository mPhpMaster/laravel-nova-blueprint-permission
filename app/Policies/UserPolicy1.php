<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy1 extends \Sereny\NovaPermissions\Policies\BasePolicy
{
	/**
	 * The Permission key the Policy corresponds to.
	 *
	 * @var string
	 */
	public $key = 'user';

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
}
