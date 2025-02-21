<?php

namespace App\Providers;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
	    \Schema::defaultStringLength(125);
	    \Gate::after(fn($user, $ability) => $user->isAnyAdmin());
        Vite::prefetch(concurrency: 3);
	    Facade::defaultAliases()->merge([
		    // 'ExampleClass' => App\Example\ExampleClass::class,
		    'App\Models\Model' => \App\Models\Abstracts\Model::class,
		    'Model'            => \App\Models\Abstracts\Model::class,
	    ])->toArray();
    }
}
