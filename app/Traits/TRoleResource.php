<?php

namespace App\Traits;

use Classes\Override;
use App\Models\Permission;
use App\Models\User;
use App\Models\User as MODEL;
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
		$userResource = Nova::resourceForModel(Override::getModelForGuard($this->guard_name));

        /** @var \App\Nova\Permission|string $permissionResource */
//        $permissionResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getPermissionClass());

		$groups = $this->loadPermissions()->map(function($permission, $key) {
			$group = getTrans(
				$g = $permission->group,
				getTrans(ucfirst($g),
						getTrans(($g = snake_case(str_singular($g))).".plural",
							getTrans("models/{$g}.plural",
								getTrans($g = snake_case(str_singular($g)),
								$permission->group,
								)
							),
						),
				),
			);

			$group_name = getTrans(
				$g = $permission->name,
				getTrans(ucfirst($g),
					getTrans($g = snake_case(str_singular($g)),
						getTrans("{$g}.plural",
							getTrans("models/{$g}.plural",
								$permission->name,
							),
						),
					),
				),
			);
			if($group_name === $permission->name) {
				$group_name = getTrans(mb_substr($permission->name, 0, mb_strlen($permission->name) - mb_strlen($permission->group)), $permission->name);
			}

			return [
				'group'  => $group,
				'option' => $permission->name,
				'label'  => $group_name,
			];
		})
			->groupBy('group')
			->toArray();

        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make(__('nova-spatie-permissions::lang.name'), 'name')
                ->rules([ 'required', 'string', 'max:255' ])
                ->creationRules('unique:' . config('permission.table_names.roles'))
				->updateRules('unique:'.config('permission.table_names.roles').',name,{{resourceId}}')
				->onlyOnForms(),

			Select::make(MODEL::trans('name'), 'name')
				->options(\App\Models\Role::getAllRolesTranslated()->all())
				->displayUsingLabels()
//				->filterable()
//				->sortable()
				->exceptOnForms(),

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
					$groups,
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
