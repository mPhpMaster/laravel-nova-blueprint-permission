<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Script;
use Laravel\Nova\Style;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return redirect()->route('nova.login');
//});

//Route::get('/panel/email/verify/{id}/{hash}', function(EmailVerificationRequest $request) {
//    $request->fulfill();
//
//    return redirect(Nova::url(Nova::$initialPath));
//})->middleware([ 'throttle:6,1', 'auth', 'signed' ])->name('verification.verify');
//
//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group([ 'middleware' => [ 'auth' ] ], function() {
	Route::get('user-logout', fn() => [ is_null((boolean)currentAuth()?->logout()), back() ][1]);
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
});

// Route::group(['middleware' => ['auth']], function () {
//     \the42coders\Workflows\Workflows::routes();
// });

Route::get('_/styles/{ext}/{name}/{dir}', function(string $ext, string $name, string $dir) {
	return getResourceAsset($name, $dir, $ext, Style::class);
})->where('dir', '.*');

Route::get('_/scripts/{ext}/{name}/{dir}', function(string $ext, string $name, string $dir) {
	return getResourceAsset($name, $dir, $ext, Script::class);
})->where('dir', '.*');
