<?php

use App\Interfaces\IRole;
use App\Models\Client;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

if(!function_exists('isLoggedIn')) {
	/**
	 * @param string|null $guard
	 *
	 * @return bool
	 */
	function isLoggedIn($guard = null): bool
	{
		return auth($guard ?? 'web')->check() ?? auth()->check() ?? false;
	}
}

if(!function_exists('isNotAnyAdmin')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return bool
	 * @throws \Throwable
	 */
	function isNotAnyAdmin(Authenticatable|null $user = null): bool
	{
		return !isAnyAdmin($user);
	}
}

if(!function_exists('isAnyAdmin')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return bool
	 * @throws \Throwable
	 */
	function isAnyAdmin(Authenticatable|null $user = null): bool
	{
		// return false;
		$user = $user ?: currentUser();
		return $user && (isSuperAdmin($user) || isAdmin($user));
	}
}

if(!function_exists('isNotAnyAdminClosure')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return \Closure
	 * @throws \Throwable
	 */
	function isNotAnyAdminClosure(Authenticatable|null $user = null): Closure
	{
		return fn() => isNotAnyAdmin($user);
	}
}

if(!function_exists('isAnyAdminClosure')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return \Closure
	 * @throws \Throwable
	 */
	function isAnyAdminClosure(Authenticatable|null $user = null): Closure
	{
		return fn() => isAnyAdmin($user);
	}
}

if(!function_exists('CurrentUserClientId')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return bool
	 * @throws \Throwable
	 */
	function CurrentUserClientId(Authenticatable|null $user = null): int|null
	{
		$user = $user ?: currentUser();
		try {
			return $user ? $user->client()->value('id') : null;
		} catch(Exception $exception) {
			return null;
		}
	}
}

if(!function_exists('CurrentUserClient')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return bool
	 * @throws \Throwable
	 */
	function CurrentUserClient(Authenticatable|null $user = null): Client|null
	{
		$user = $user ?: currentUser();
		try {
			return $user?->client;
		} catch(Exception $exception) {
			return null;
		}
	}
}

if(!function_exists('isAdmin')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return bool
	 * @throws \Throwable
	 */
	function isAdmin(Authenticatable|null $user = null): bool
	{
		$user ??= currentUser();
		throw_if($user && !method_exists($user, 'isAdmin'), 'Method [isAdmin] missing in '.getClass($user));

		return $user?->isAdmin();
	}
}

if(!function_exists('isSuperAdmin')) {
	/**
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 *
	 * @return bool
	 * @throws \Throwable
	 */
	function isSuperAdmin(Authenticatable|null $user = null): bool
	{
		$user ??= currentUser();
		throw_if($user && !method_exists($user, 'isSuperAdmin'), 'Method [isSuperAdmin] missing in '.getClass($user));

		return $user?->isSuperAdmin();
	}
}

if(!function_exists('getGuardForModel')) {
	/**
	 * Get the session auth guard for the model.
	 *
	 * @param class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model $model
	 *
	 * @return string|null
	 */
	function getGuardForModel(string|object $model): ?string
	{
		if(is_object($model)) {
			$model = get_class($model);
		}

		$provider = collect(config('auth.providers'))->reject(function($provider) use ($model) {
			return !($provider['driver'] === 'eloquent' && is_a($model, $provider['model'], true));
		})->keys()->first();

		return collect(config('auth.guards'))->reject(function($guard) use ($provider) {
			return !($guard['driver'] === 'session' && $guard['provider'] === $provider);
		})->keys()->first() ?? getDefaultGuardName();
	}
}

if(!function_exists('getGuardsForModel')) {
	/**
	 * Return a collection of guard names suitable for the $model,
	 * as indicated by the presence of a $guard_name property or a guardName() method on the model.
	 *
	 * @param string|Model $model model class object or name
	 *
	 * @return Collection
	 */
	function getGuardsForModel($model): Collection
	{
		$class = is_object($model) ? get_class($model) : $model;

		if(is_object($model)) {
			if(\method_exists($model, 'guardName')) {
				$guardName = $model->guardName();
			} else {
				$guardName = $model->getAttributeValue('guard_name');
			}
		}

		if(!isset($guardName)) {
			$guardName = (new \ReflectionClass($class))->getDefaultProperties()['guard_name'] ?? null;
		}

		if($guardName) {
			return collect($guardName);
		}

		return collect(config('auth.guards'))
			->map(function($guard) {
				if(!isset($guard['provider'])) {
					return null;
				}

				return config("auth.providers.{$guard['provider']}.model");
			})
			->filter(function($model) use ($class) {
				return $class === $model;
			})
			->keys();
	}
}

if(!function_exists('getDefaultGuardName')) {
	/**
	 * @param mixed|null $defaults
	 *
	 * @return string|null
	 */
	function getDefaultGuardName(mixed $defaults = null): string|null
	{
		return config('auth.defaults.guard') ?: value($defaults);
	}
}

if(!function_exists('getModel')) {
	/**
	 * Returns model/class of query|model|string.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Model|string $model
	 *
	 * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Model|string|null
	 */
	function getModel($model)
	{
		try {
			if(is_object($model)) {
				/** @var \Illuminate\Database\Eloquent\Builder */
				return $model->getModel();
			}
			throw new Exception($model);
		} catch(Exception $exception) {
			try {
				if(is_object($model)) {
					/** @var \Illuminate\Database\Eloquent\Model */
					return $model->getQuery()->getModel();
				}
				throw new Exception($model);
			} catch(Exception $exception2) {
				try {
					return getModelClass($model);
				} catch(Exception $exception3) {

				}
			}
		}

		return null;
	}
}

if(!function_exists('getModelClass')) {
	/**
	 * Returns model class of query|model|string.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Model|string $model
	 *
	 * @return string|null
	 */
	function getModelClass($model)
	{
		try {
			$_model = !is_string($model) ? getClass($model) : $model;
			if(!class_exists($_model)) {
				if(!class_exists($__model = "\\App\\Models\\{$_model}")) {
					try {
						$__model = getClass(app($_model));
					} catch(\Exception $exception2) {
						try {
							$__model = getRealClassName($_model);
						} catch(\Exception $exception3) {
							$__model = null;
						}
					}
				}

				$_model = trim(is_string($__model) ? $__model : getClass($__model));
			}
		} catch(Exception $exception1) {

		}

		if($_model) {
			$_model = isModel($_model) ? $_model : null;
		}

		return $_model ?? null;
	}
}

if(!function_exists('isModel')) {
	/**
	 * Determine if a given object is inherit Model class.
	 *
	 * @param object $object
	 *
	 * @return bool
	 */
	function isModel($object): bool
	{
		try {
			return (is_object($object) && $object instanceof Model) ||
				is_subclass_of($object, Model::class) ||
				is_a($object, Model::class);
		} catch(Exception $exception) {

		}

		return false;
	}
}
