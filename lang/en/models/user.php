<?php

use App\Interfaces\IRole;

return [
	'singular' => $singular = 'User',
	'plural'   => $plural = 'Users',
	'user'     => $singular,
	'users'    => $plural,
    'fields' => [
        'id' => 'ID',
        'name' => 'Full Name',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Password Confirmation',
        'phone' => 'Phone',
        'position' => 'Position',
        'user_type' => 'Type',
        'email_verified_at' => 'Verified At',
        'email_verified' => 'Verified',
        'email_verified_success' => 'Verified',
        'email_verified_danger' => 'Not Verified',
        'roles' => 'Roles',
        'permissions' => 'Permissions',
        'deleted_at' => 'Deleted At',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'AVATAR'       => 'AVATAR',
        'phone_number' => 'Phone Number',
        'role'         => 'Role',
        'projects'     => 'Projects',
        'image'        => 'Avatar'
    ],
	'roles' => [
//		IRole::AdminRole      => 'Admin',
//		IRole::SupervisorRole => 'Supervisor',
//		IRole::ForemanRole    => 'Foreman',
//		IRole::EmployeeRole   => 'Employee',
	]
];
