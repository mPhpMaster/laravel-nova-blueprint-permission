<?php

use App\Interfaces\IRole;

return [
	'user'     => 'مستخدم',
	'users'    => 'مستخدمين',
	'singular' => 'المستخدم',
	'plural'   => 'المستخدمين',
    'fields' => [
	    'id' => 'م',
	    'name' => 'الإسم',
        'first_name' => 'الإسم الأول',
        'last_name' => 'الإسم الأخير',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone' => 'الجوال',
        'position' => 'المركز/الموقع',
        'user_type' => 'النوع',
        'email_verified_at' => 'تاريخ التحقق',
        'email_verified' => 'حالة التحقق',
        'email_verified_success' => 'تم التحقق',
        'email_verified_danger' => 'فشل التحقق',
        'roles' => 'مجموعات الصلاحيات',
        'permissions' => 'الصلاحيات',
        'deleted_at' => 'تاريخ الحذف',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'AVATAR'       => 'الصورة',
        'phone_number' => 'رقم الجوال',
        'role'         => 'المنصب',
        'projects'     => 'المشاريع',
        'image'        => 'الصورة الرمزية'
    ],
	'roles' => [
//		IRole::AdminRole      => 'مدير',
//		IRole::SupervisorRole => 'مشرف',
//		IRole::ForemanRole    => 'مراقب عمال',
//		IRole::EmployeeRole   => 'موظف',
	]
];
