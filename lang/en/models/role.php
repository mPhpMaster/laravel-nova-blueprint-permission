<?php

use App\Interfaces\IRole;

return [
	'role'  => 'User Group',
	'roles' => 'User Groups',
    'plural' => 'Roles',
    'singular' => 'Role',
    'fields' => [
        'name' => 'Name',
        'guard_name' => 'Guard Name',
        'permissions' => 'Permissions',
		'users' => 'Users',
    ],
	IRole::SuperAdminRole => 'Super Admin',
	IRole::AdminRole => 'Administrator',
];
