<?php

namespace App\Providers;

use App\Models\User;
use App\Nova\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
	public static function bootMenu(?ServiceProvider $provider = null)
	{
		Nova::enableRTL(fn() => $provider->app->getLocale() === 'ar' || currentLocale() === 'ar');
		Nova::withBreadcrumbs(fn() => auth()->check());
		Nova::sortResourcesBy(function($resource) {
			return $resource::$priority ?? 9999;
		});

//		if($provider->app->getLocale() === 'ar') {
//			Nova::enableRTL();
//		}

		// Nova::mainMenu(fn(Request $request) => [
		//     MenuSection::dashboard(Main::class)->icon('chart-bar'),
		//
		//     MenuSeparator::make(),
		//
		//     MenuGroup::make(
		//         __('Administration'),
		//         [
		//             MenuSection::make(
		//                 __('User Management'),
		//                 [
		//                     MenuItem::resource(User::class),
		//                     MenuItem::link(__('nova-spatie-permissions::lang.sidebar_label_roles'), 'resources/roles'),
		//                     MenuItem::link(__('nova-spatie-permissions::lang.sidebar_label_permissions'), 'resources/permissions'),
		//                 ]
		//             )->icon('users')->collapsable(),
		//         ]
		//     )->collapsable(),
		//
		// ]);
	}

	public static function bootUserMenu(?ServiceProvider $provider = null)
	{
		Nova::userMenu(function(Request $request, Menu $menu) {
			if($request->user()) {
				$menu->prepend(
					MenuItem::make(
						__('Profile'),
						UserProfile::getResourceUrl(getNovaRequest(), $request->user(), '', 'edit'),
					),
				);
			}

			return $menu;
		});
	}

	public static function bootFooter(?ServiceProvider $provider = null)
	{
		Nova::footer(function($request) {
			$devMode = '';
			if(isDeveloperMode()) {
				$devMode = "<span class='text-green-600'>DevMode</span>";
				$devMode .= " <span class='text-primary-800'>(".getDeveloper().')</span>';
			}
			$devMode = $devMode ? "{$devMode}" : '';

			$version = '';
			if(\File::exists($composerPath = base_path('composer.json'))) {
				try {
					$version = "<span class='text-xxs'> v";
					$version .= data_get(json_decode(\File::get($composerPath), true) ?: [], 'version');
					$version .= '</span>';
				} catch(\Exception|\Error $exception) {
				}
			}
			$version = $devMode ? " - {$version}" : $version;

			return Blade::render("<div class='text-xxs'>@env('local') {$devMode} @endenv {$version}</div>");
		});

		// \Laravel\Nova\Nova::footer(function($request) {
		//     return Blade::render(
		//         '
		//     @env(\'local\')
		//         This is Local!
		//     @endenv
		//     @env(\'prod\')
		//         This is Production!
		//     @endenv
		// '
		//     );
		// });
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		parent::boot();

		Password::defaults(Password::min(3));
		$this->loadTranslationsFrom(__DIR__.'/../lang/nova-spatie-permissions', 'nova-spatie-permissions');

		// Nova::withBreadcrumbs();
		// Nova::withoutThemeSwitcher();
		Nova::serving(function(ServingNova $event) {
			Nova::translations(lang_path("vendor/nova/".app()->getLocale().".json"));
			// loading custom files
			// Nova::script('js-helpers', resource_path("js/helpers.js"));
			// Nova::style('css-helpers', resource_path("css/helpers.css"));
		});

		static::bootMenu($this);
		static::bootUserMenu($this);
		static::bootFooter($this);

		// Nova::enableRTL();

		Nova::resourcesIn(app_path("Nova"));
		Nova::notificationPollingInterval(5);
	}

	/**
	 * Get the tools that should be listed in the Nova sidebar.
	 *
	 * @return array<int, \Laravel\Nova\Tool>
	 */
	public function tools(): array
	{
		return [
			\Badinansoft\LanguageSwitch\LanguageSwitch::make(),

			\Packages\NovaPermissions\NovaPermissions::make(),

			//			(new \Sereny\NovaPermissions\NovaPermissions())
			//				->disableMenu()
			//			->disablePermissions()
			//				->canSee(function($request) {
			//					return $request->user()->isSuperAdmin();
			//				}),
		];
	}

	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		parent::register();

		//
	}

	/**
	 * Register the configurations for Laravel Fortify.
	 */
	protected function fortify(): void
	{
		Nova::fortify()
			->features([
				Features::updatePasswords(),
				Features::emailVerification(),
				Features::twoFactorAuthentication([ 'confirm' => true, 'confirmPassword' => true ]),
			])
			->register();
	}

	/**
	 * Register the Nova routes.
	 */
	protected function routes(): void
	{
		Nova::routes()
			->withAuthenticationRoutes(default: true)
			->withPasswordResetRoutes()
//			->withoutEmailVerificationRoutes()
			->register();
	}

	/**
	 * Register the Nova gate.
	 *
	 * This gate determines who can access Nova in non-local environments.
	 */
	protected function gate(): void
	{
		Gate::define('viewNova', function(User $user) {
			return isDeveloperMode() || in_array($user->email, [
					'admin@app.com',
				]) || $user?->isAnyAdmin();
		});
	}

	/**
	 * Get the dashboards that should be listed in the Nova sidebar.
	 *
	 * @return array<int, \Laravel\Nova\Dashboard>
	 */
	protected function dashboards(): array
	{
		return [
			new \App\Nova\Dashboards\Main,
		];
	}
}
