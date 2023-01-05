<?php

use App\Interfaces\IUserType;
use Carbon\Carbon;

/**
 * @noinspection PhpIllegalPsrClassPathInspection
 * @internal
 */
class InConfigParser
{
    public static function permissionOf(string $name, string $group = "system", string $guard_name = "web"): array
    {
        $guard_name = "web";
        if( !stringContains($name, '.') ) {
            return static::permissionsOf($name, $group, $guard_name);
        }
        $name = snake_case($name, '-');

        return [ "name" => "{$name}", "group" => "{$group}", "guard_name" => "{$guard_name}" ];
    }

    public static function permissionsOf(string $name, string $group = "system", string $guard_name = "web"): array
    {
        $guard_name = "web";
        if( stringContains($name, '.') ) {
            return [ static::permissionOf($name, $group, $guard_name) ];
        }
        $name = snake_case($name, '-');
        return [
            [ "name" => "{$name}.index", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.view_any", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.view", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.create", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.edit", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.delete", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.restore", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
            [ "name" => "{$name}.force_delete", "group" => "{$group}", "guard_name" => "{$guard_name}" ],
        ];
    }

    public static function roleOf(string $name, string $group = "system", string $guard_name = "web"): array
    {
        $guard_name = "web";

        return compact('name', 'group', 'guard_name');
    }

    public static function userOf(
        string $name,
        ?string $password = null,
        ?string $email = null,
        mixed $email_verified_at = 0,
    ): array {
        $email_verified_at = $email_verified_at === 0 ? now() : Carbon::parse($email_verified_at);
        $email ??= "{$name}@app.com";
        $password = bcrypt($password ?? $email ?? '12345678');

        return compact([
                           'name',
                           'password',
                           'email',
                           'email_verified_at',
                       ]);
    }
}
