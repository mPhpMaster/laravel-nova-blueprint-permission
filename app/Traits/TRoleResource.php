<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Sereny\NovaPermissions\Fields\Checkboxes;
use Spatie\Permission\PermissionRegistrar;

/**
 * @mixin \App\Nova\Role
 */
trait TRoleResource
{
	/**
	 * @return \Spatie\Permission\Contracts\Role
	 */
	public static function getModel()
	{
		return app(PermissionRegistrar::class)->getRoleClass();
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
		$guardOptions = collect(config('auth.guards'))->mapWithKeys(function($value, $key) {
			return [ $key => $key ];
		});

		/** @var \App\Nova\User|string $userResource */
		$userResource = Nova::resourceForModel(\App\Helpers\Classes\Override::getModelForGuard($this->guard_name));

		/** @var \App\Nova\Permission|string $permissionResource */
		// $permissionResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass());

		return [
			ID::make(__('ID'), 'id')->sortable(),

			Text::make(__('nova-spatie-permissions::lang.name'), 'name')
				->rules([ 'required', 'string', 'max:255' ])
				->creationRules('unique:'.config('permission.table_names.roles'))
				->updateRules('unique:'.config('permission.table_names.roles').',name,{{resourceId}}'),

			Hidden::make(__('nova-spatie-permissions::lang.guard_name'), 'guard_name')
				->default(fn() => $this->guard ?: config('nova.guard') ?: config('auth.defaults.guard')),

			Select::make(__('nova-spatie-permissions::lang.guard_name'), 'guard_name')
				->options($guardOptions->toArray())
				->rules([ 'required', Rule::in($guardOptions) ])
				// ->hideFromDetail()
				->hideFromIndex()
				->hideWhenCreating()
				->hideWhenUpdating(),

			DateTime::make(__('nova-spatie-permissions::lang.created_at'), 'created_at')
				->exceptOnForms(),

			// DateTime::make(__('nova-spatie-permissions::lang.updated_at'), 'updated_at')
			//         ->exceptOnForms(),

			// BelongsToMany::make($permissionResource::label(), 'permissions', $permissionResource)->searchable(),

			Checkboxes::make(Permission::trans('plural'), 'permissions')
				->options(
					$this->loadPermissions()->map(function($permission, $key) {
						return [
							'group'  => __(ucfirst($permission->group)),
							'option' => $permission->name,
							'label'  => __($permission->name),
						];
					})
						->groupBy('group')
						->toArray()
				),

			Text::make(User::trans('plural'), fn() => $this->users()->count())
				->exceptOnForms(),

			MorphToMany::make($userResource::label(), 'users', $userResource)->searchable(),
		];
	}

	/**
	 * Load all permissions
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	protected function loadPermissions()
	{
		/** @var class-string */
		$permissionClass = config('permission.models.permission');

		return $permissionClass::all();
	}
}
