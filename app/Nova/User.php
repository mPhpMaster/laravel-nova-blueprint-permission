<?php

namespace App\Nova;

use App\Models\User as MODEL;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Illuminate\Http\Request;
use Laravel\Nova\Auth\PasswordValidationRules;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\PasswordConfirmation;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @mixin MODEL
 */
class User extends Resource
{
    use PasswordValidationRules;

	public static int $priority = 0;

	/**
	 * The logical group associated with the resource.
	 *
	 * @var string
	 */
	public static $group = 'Administration';

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = MODEL::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

	public function subtitle()
	{
		return static::trans('user_role', [ 'role' => $this->role ]);
	}

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field|\Laravel\Nova\Panel|\Laravel\Nova\ResourceTool|\Illuminate\Http\Resources\MergeValue>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(MODEL::trans('id'), 'id')->sortable(),

            // Gravatar::make()->maxWidth(50),

            Text::make(MODEL::trans('name'), 'name')
				->sortable(),

            Text::make(MODEL::trans('email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make(MODEL::trans('password'), 'password')
                ->onlyOnForms()
                ->creationRules($this->passwordWithConfirmedRules())
                ->updateRules([...$this->optionalPasswordRules(), 'confirmed']),
//				->creationRules('required', Rules\Password::defaults(), 'confirmed')
//				->updateRules('nullable', Rules\Password::defaults(), 'confirmed'),
            PasswordConfirmation::make(MODEL::trans('password_confirmation'), 'password_confirmation'),

            Select::make(MODEL::trans('role'), 'role')
	            ->options(\App\Models\Role::getAllRolesTranslated()->all())
	            ->displayUsingLabels()
//				->filterable()
//				->sortable()
	            ->exceptOnForms(),

            // Select::make(MODEL::trans('user_type'), 'user_type')
            //       ->rules('required', 'string', Rule::in(User::getAllUserTypes()))
            //       ->options(array_combine(MODEL::getAllUserTypes(), MODEL::getAllUserTypes()))
            //       ->default(IUserType::NORMAL),

            DateTime::make(MODEL::trans('created_at'), 'created_at')
	            ->onlyOnIndex(),

            Badge::make(MODEL::trans('email_verified'), fn() => $this->hasVerifiedEmail() ? 'success' : 'danger')
	            ->labels([
		            'danger'  => MODEL::trans('email_verified_danger'),
		            'success' => MODEL::trans('email_verified_success'),
	            ])
	            ->withIcons()
	            ->hideFromIndex(),

            MorphToMany::make(MODEL::trans('roles'), 'roles', Role::class),

            MorphToMany::make(MODEL::trans('permissions'), 'permissions', Permission::class),
			//            MorphToMany::make(Role::trans('plural'), 'roles', \Sereny\NovaPermissions\Nova\Role::class)
			//	            ->showOnIndex(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [
            ...parent::actions(getNovaRequest()::createFrom($request)),
        ];
    }
}
