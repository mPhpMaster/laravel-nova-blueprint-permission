<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [ AuthController::class, 'login' ])
     ->name('login');

Route::middleware('auth:sanctum')
     ->namespace("App\Http\Controllers\Api")
     ->group(function() {
         Route::get('user', [ AuthController::class, 'currentUser' ])->name('auth.current_user');

         Route::post('logout', [ AuthController::class, 'logout' ])->name('auth.logout');
         Route::post('account/change_password', [ AuthController::class, 'changePassword' ])->name('auth.change_password');
         Route::put('profile', [ AuthController::class, 'updateProfile' ])->name('auth.update_profile');

         apiResources([
                          'users' => UserController::class,
                          'roles' => RoleController::class,
                          'permissions' => PermissionController::class,
                      ]);
     });
