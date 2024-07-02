<?php

namespace App\Traits;

use App\Helpers\Classes\Override;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsToMany;
use Spatie\Permission\PermissionRegistrar;

/**
 * @mixin \App\Nova\Permission
 */
trait TPermissionResource
{
    /**
     * @return \Spatie\Permission\Contracts\Permission
     */
    public static function getModel()
	{
		return app(PermissionRegistrar::class)->getPermissionClass();
	}

	/**
	 * Get the fields displayed by the resource.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function fields(Request $request)
	{
		$guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
			return [$key => $key];
		});

        /** @var \App\Nova\User|string $userResource */
		$userResource = Nova::resourceForModel(Override::getModelForGuard($this->guard_name));

        /** @var \App\Nova\Role|string $roleResource */
		$roleResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass());

		return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make(__('nova-spatie-permissions::lang.name'), 'name')
				->rules(['required', 'string', 'max:255'])
				->creationRules('unique:' . config('permission.table_names.permissions'))
				->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

            Text::make(__('nova-spatie-permissions::lang.display_name'), fn() => __('nova-spatie-permissions::lang.display_names.'.$this->name))
                ->canSee(fn() => is_array(__('nova-spatie-permissions::lang.display_names'))),

            Select::make(__('nova-spatie-permissions::lang.guard_name'), 'guard_name')
				->options($guardOptions->toArray())
				->rules(['required', Rule::in($guardOptions)]),

            DateTime::make(__('nova-spatie-permissions::lang.created_at'), 'created_at')->exceptOnForms(),

            DateTime::make(__('nova-spatie-permissions::lang.updated_at'), 'updated_at')->exceptOnForms(),

            BelongsToMany::make($roleResource::label(), 'roles', $roleResource)->searchable(),

            MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
		];
	}
}
