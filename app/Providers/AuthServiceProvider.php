<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Automatically finding the Policies
        // Gate::guessPolicyNamesUsing(function($modelClass) {
        //     return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        // });

        // Implicitly grant "Super Admin" role all permission checks using can() todo: later: active it
        if( !isRunningInConsole() ) {
            Gate::before(function($user, $ability) {
                return $user->isSuperAdmin() ? true : null;
            });
        }
    }
    /**
     * Get the policies defined on the provider.
     *
     * @return array<class-string, class-string>
     */
    public function policies()
    {
        return array_merge(static::getAllPolices(), $this->policies);
    }

    /**
     * @return array
     */
    public static function getAllPolices(): array
    {
        return toCollect(
            \Symfony\Component\Finder\Finder::create()
                                            ->files()
                                            ->in(app_path("Policies"))
                                            ->notPath(DIRECTORY_SEPARATOR . "Abstracts" . DIRECTORY_SEPARATOR)
        )
            ->map(fn(\Symfony\Component\Finder\SplFileInfo $file) => rtrim($file->getFilenameWithoutExtension(), "Policy"))
            ->map(fn(string $model) => getClass(class_exists($_class = "\\App\\Models\\{$model}") ? $_class : app($model)))
            ->mapWithkeys(function(string $model) {
                return [
                    $model => get_class(policy($model)),
                ];
            })
            ->filter()
            ->toArray();
    }
}
