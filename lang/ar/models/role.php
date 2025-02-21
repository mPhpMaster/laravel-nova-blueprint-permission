<?php

use App\Interfaces\IRole;

return [
	'role'  => 'مجموعة المستخدمين',
	'roles' => 'مجموعات المستخدمين',
    'plural' => 'مجموعات الصلاحيات',
    'singular' => 'مجموعة الصلاحيات',
    'fields' => [
        'name' => 'اسم',
        'guard_name' => 'اسم الحارس',
        'permissions' => 'أذونات',
        'users' => 'المستخدمين',
    ],
	IRole::SuperAdminRole => 'مدير النظام',
	IRole::AdminRole => 'مدير',
    // 'import' => 'استيراد مجموعات الصلاحيات',
];
