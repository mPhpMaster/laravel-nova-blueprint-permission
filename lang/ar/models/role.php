<?php

use App\Interfaces\IRole;

return [
	'role'     => 'وظيفة',
	'roles'    => 'وظائف',
	'singular' => 'الوظيفة',
	'plural'   => 'الوظائف',
	'fields'   => [
		'name'        => 'اسم',
		'guard_name'  => 'اسم الحارس',
		'permissions' => 'الصلاحيات',
		'users'       => 'المستخدمين',

//		IRole::AdminRole      => 'مدير',
//		IRole::SupervisorRole => 'مشرف',
//		IRole::ForemanRole    => 'مراقب',
//		IRole::EmployeeRole   => 'موظف',
	]
];
