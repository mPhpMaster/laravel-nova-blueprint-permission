<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 *
 */
class AuthController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function currentUser(Request $request)
    {
        $data = $request->user()->loadMissing([
                                                  'groups.roles.permissions',
                                                  'roles.permissions',
                                                  'permissions',
                                              ]);

        $result = UserResource::makeSuccess($data);

        if( isDeveloperMode() ) {
            $result = $result->trueOptions('all_permissions', 'all_roles');
        }

        return $result;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $validated = $request->validated();

        $user = currentUser();
        $user->password = $validated[ 'new_password' ];
        $user->save();

        $result = UserResource::makeSuccess($user->refresh());

        if( isDeveloperMode() ) {
            $result = $result->trueOptions('all_permissions', 'all_roles');
        }

        return $result;
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Http\Resources\Abstracts\AbstractResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
                                              'email' => 'required|email',
                                              'password' => 'required',
                                          ]);

        if( !$this->guard()->attempt($credentials, $request->boolean('remember')) ) {
            throw ValidationException::withMessages([
                                                        'email' => [ trans('auth.failed') ],
                                                    ]);
        }

        $user = $request->user() ?? auth()->user() ?? User::whereEmail($request->email)->firstOrFail();
        $user->addActivityLog("login");

        $token = $user->createToken($request->token_name ?? 'auth-token')->plainTextToken;

        $result = UserResource::makeSuccess($user)
                              ->additionalData(compact('token'));

        if( isDeveloperMode() ) {
            $result = $result->trueOptions('all_permissions', 'all_roles');
        }

        return $result;
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $accesssToken =  $request->user()->currentAccessToken();
            throw_unless(is_callable($accesssToken), new \Exception(''));
              $accesssToken->delete();


        } catch(\Exception $exception) {
            $request->user()->tokens()->delete();
        }
        $request->user()->addActivityLog("logout");
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->sendSuccess(null, null, 204);
    }

    /**
     * @param $lang
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emailVerify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * @param \App\Http\Requests\UpdateProfileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource|string|null
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        currentUser()->update($request->validated());

        return UserResource::makeSuccess(currentUser()->refresh());
    }
}
