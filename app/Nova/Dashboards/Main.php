<?php

namespace App\Nova\Dashboards;

use Abordage\TotalCard\TotalCard;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(): array
    {
        return [
	        // region: Users
	        (new TotalCard(User::class, User::trans('plural')))
		        ->width('1/3')
		        ->canSee(fn(Request $request) => canViewAny(User::class)),
	        // endregion: Users
        ];
    }

	public function uriKey()
	{
		return snake_case(class_basename(static::class), '-');
	}

	public function name()
	{
		return __('homePage');
	}
}
