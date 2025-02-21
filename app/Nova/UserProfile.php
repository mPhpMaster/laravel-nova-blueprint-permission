<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\UserProfile as MODEL;

/**
 *
 */
class UserProfile extends User
{
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
        'name',
        'email',
    ];

    public static function label()
    {
        return static::trans('singular');
    }

    public static function singularLabel()
    {
        return static::trans('singular');
    }

    public static function uriKey()
    {
        return 'user-profile';
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return static::getResourceUrl($request, $resource, "", "edit");
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
	public function fields(NovaRequest $request): array
	{
        return [
//            Text::make(static::trans('login_id'), 'login_id')
//                ->rules('required', 'max:255', 'string')
//                ->placeholder(static::trans('login_id'))
//                ->withMeta([ 'textAlign' => 'center' ])
//                ->maxlength(20, true),

            Text::make(static::trans('name'), 'name')
                ->rules('required', 'max:255', 'string')
                ->placeholder(static::trans('name'))
                ->withMeta([ 'textAlign' => 'center' ])
                ->maxlength(30, true),

            Email::make(static::trans('email'), 'email')
                 ->creationRules('required', 'unique:users,email', 'email')
                 ->updateRules(
                     'required',
                     'unique:users,email,{{resourceId}}',
                     'email'
                 )
                 ->exceptOnForms()
                 ->placeholder(static::trans('email')),

            Password::make(static::trans('password'), 'password')
                    ->onlyOnForms()
                    ->rules('nullable', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'),

            \Laravel\Nova\Fields\PasswordConfirmation::make(static::trans('confirmation'), 'password'),

			Badge::make(MODEL::trans('email_verified'), fn() => $this->hasVerifiedEmail() ? 'success' : 'danger')
				->labels([
					'danger'  => MODEL::trans('email_verified_danger'),
					'success' => MODEL::trans('email_verified_success'),
				])
				->withIcons()
				->hideFromIndex(),

        ];
    }
}
