<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Nova\Nova;
use Laravel\Nova\Script;
use Laravel\Nova\Style;

//Route::redirect("/", Nova::path());
//Route::get('/', function() {
//	return redirect()->route('nova.login');
//});

Route::group([ 'middleware' => [ 'auth' ] ], function() {
	Route::get('user-logout', fn() => [ is_null((boolean) currentAuth()?->logout()), back() ][1]);
	Route::get('change-language', function() {
		$oldLocale = currentLocale();
		$nextLocale = fn($v) => $oldLocale !== $v;
		$locales = array_filter(getLocales(), $nextLocale);
		$locale = array_get($locales, (key(array_where($locales, $nextLocale)) ?? 0), getDefaultLocale()) ?: getDefaultLocale();
		setCurrentLocale($locale);
		$key = auth()->guard(config('nova.guard'))->id().'.locale';
		Cache::forever($key, $locale);

		return response()->noContent();
	});
	Route::get('export-data/{data}', function(\Illuminate\Http\Request $request, string $data) {
		$data = json_decode(urldecode(base64_decode($data, true)), true);
		dd([ $request->all(), $data ]);
		return;
	})->name('export-data')->where('data', '.*');
});
//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::get('_/styles/{ext}/{name}/{dir}', function(string $ext, string $name, string $dir) {
	return getResourceAsset($name, $dir, $ext, Style::class);
})->where('dir', '.*');

Route::get('_/scripts/{ext}/{name}/{dir}', function(string $ext, string $name, string $dir) {
	return getResourceAsset($name, $dir, $ext, Script::class);
})->where('dir', '.*');

Route::get('/panel/email/verify/{id}/{hash}', function(EmailVerificationRequest $request) {
	$request->fulfill();

	return redirect(Nova::url(Nova::$initialPath));
})->middleware([ 'throttle:6,1', 'auth', 'signed' ])->name('verification.verify');

//Route::get('/home', [ App\Http\Controllers\HomeController::class, 'index' ])->name('home2');

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});

//require __DIR__.'/auth.php';

if(isDeveloperMode()) {
	// quick login
	Route::get('login-as/{id}', \App\Http\Controllers\Auth\LoginAsController::class);
}