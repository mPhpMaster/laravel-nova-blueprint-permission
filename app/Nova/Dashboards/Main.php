<?php

namespace App\Nova\Dashboards;

use Abordage\TotalCard\TotalCard;
use App\Interfaces\IRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jubeki\Nova\Cards\Linkable\Linkable;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    public function cards()
    {
        return [
	        // The width of the card (1/3, 2/3, 1/2, 1/4, 3/4, or full).
	        // Users cards
	        (new Linkable)
                ->title(__('users'))
                ->url('/admin/resources/users')
                ->width('full')
		        ->canSee(fn(Request $request) => isAnyAdmin()),

	        (new TotalCard(User::class, __('users')))->width('1/3')
		        ->canSee(fn(Request $request) => isAnyAdmin()),

//	        (new TotalCard(User::ByRole(IRole::SuperAdminRole), __('admins')))->width('1/3')
//                ->canSee(fn(Request $request) => isAnyAdmin()),

	        // Tasks cards
//	        (new Linkable)
//                ->title(__('tasks'))
//                ->url('/admin/resources/tasks')
//                ->width('full'),
	        //            (new TotalCard(Task::class, __('tasks')))->width('1/3'),
	        //            (new TotalCard(Task::ByStatus(true), __('checkedTasks')))->width('1/3'),
	        //            (new TotalCard(Task::ByStatus(false), __('uncheckedTasks')))->width('1/3'),
        ];
    }

    public function uriKey()
    {
        return snake_case(class_basename(static::class), "-");
    }

    public function name()
    {
        return __('homePage');
    }
}