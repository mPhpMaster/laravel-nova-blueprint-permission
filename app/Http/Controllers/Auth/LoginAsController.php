<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 *
 */
class LoginAsController extends Controller
{
	/**
	 * @param \Illuminate\Http\Request $request
	 * @param                          $id
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function __invoke(Request $request, $id)
	{
		abort_unless(isDeveloperMode(), 503);

		$user = User::findOrFail($id === 'x' ? 1 : $id);
		if(currentUserId() != $user->id) {
			logout();
			login($user);
		}

		return back();
	}
}
