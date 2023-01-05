<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\FilesystemAdapter;

if( !function_exists('toCollect') ) {
    /**
     * Create a new collection from the given value if its wasn't collection.
     *
     * @param mixed $value
     *
     * @return \Illuminate\Support\Collection
     */
    function toCollect($value = null): \Illuminate\Support\Collection
    {
        $value = isModel($value) ? [ $value ] : $value;

        return is_collection($value) ? $value : collect($value);
    }
}

/**
 * @return string|null
 */
function getLastCalledClass(): ?string
{
    return data_get(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4), '2.class');
}

if( !function_exists('hasDeveloper') ) {
    /**
     * Check if exists DEVELOPER value in .env.
     *
     * @param string|null $developer
     *
     * @return bool
     */
    function hasDeveloper(?string $developer = null): bool
    {
        return filled($dev = getDeveloper()) && ( !$developer || $developer === $dev);
    }
}

if( !function_exists('getDeveloper') ) {
    /**
     * @return string
     */
    function getDeveloper(): string
    {
        return trim(config('app.developer') ?: "");
    }
}

if( !function_exists('isDeveloper') ) {
    /**
     * Check DEVELOPER value in .env.
     *
     * @param string|null $developer
     *
     * @return bool
     */
    function isDeveloper(?string $developer = null): bool
    {
        return isDeveloperMode() && hasDeveloper($developer);
    }
}

if( !function_exists('isDeveloperMode') ) {
    /**
     * Check if dev mode is active.
     *
     * @return bool
     */
    function isDeveloperMode(): bool
    {
        return config('app.dev_mode', false);
    }
}

if( !function_exists('currencyNumberFormat') ) {
    /**
     * @param int|float|string|\Closure $value
     * @param array                     $options [currency = null, locale = null, digits = 2]
     *
     * @return string
     */
    function currencyNumberFormat(
        $value,
        array $options = [
            'currency' => null,
            'locale' => 'en',
            'digits' => 2,
        ],
        $getterMethod = 'last'
    ): string {
        return trim($getterMethod(explode(" ", trim(currencyFormat($value, $options)))));
    }
}

if( !function_exists('numberFormat') ) {
    /**
     * @param int|float|string|\Closure $value
     *
     * @return string
     */
    function numberFormat($value): string
    {
        return trim(number_format($value, str_contains(trim($value), '.') ? 2 : 0));
    }
}

if( !function_exists(function: 'trimDirectorySeparator') ) {
    /**
     * @param string $path
     *
     * @return string
     */
    function trimDirectorySeparator($path): string
    {
        return trim($path, DIRECTORY_SEPARATOR);
    }
}

if( !function_exists('buildPath') ) {
    /**
     * @param string ...$path
     *
     * @return string
     */
    function buildPath(...$paths): string
    {
        $result = implode(
            DIRECTORY_SEPARATOR,
            array_map(fn($v) => trimDirectorySeparator($v), $paths)
        );

        return trim($result, DIRECTORY_SEPARATOR);
    }
}

if( !function_exists('str_before_last_count') ) {
    /**
     * Get the portion of a string before the last occurrence of a given value for X times.
     *
     * @param string $subject
     * @param string $search
     * @param int    $times
     *
     * @return string
     */
    function str_before_last_count($subject, $search, int $times = 1): string
    {
        $times = $times > 0 ? $times : 0;
        $result = $subject;
        while( $times && $times-- ) {
            $result = \Illuminate\Support\Str::beforeLast($result, $search);
        }

        return $result;
    }
}

if( !function_exists('getDefaultDiskDriver') ) {
    /**
     * @param $default
     *
     * @return string
     */
    function getDefaultDiskDriver($default = 'local'): string
    {
        return config('filesystems.default', ($default ?: 'public'));
    }
}

if( !function_exists('getPaymentGatewaysDiskDriver') ) {
    /**
     * @param $default
     *
     * @return string
     */
    function getPaymentGatewaysDiskDriver($default = 'public'): string
    {
        return config('filesystems.payment_gateways', ($default ?: 'public'));
    }
}

if( !function_exists('getProfilePhotoDiskDriver') ) {
    /**
     * @param $default
     *
     * @return string
     */
    function getProfilePhotoDiskDriver($default = 'public'): string
    {
        return config('filesystems.profile_image', ($default ?: 'public'));
    }
}

/**
 * @param string|null $default
 *
 * @return string
 */
function getDefaultMediaLibraryDiskDriver($default = 'local'): string
{
    return trim(config('nova-media-library.disk', getDefaultDiskDriver($default)));
}

/**
 * @param string|null $default
 *
 * @return \Illuminate\Filesystem\FilesystemAdapter
 */
function getDefaultMediaLibraryDiskStorage($default = 'local'): FilesystemAdapter
{
    return \Storage::disk(getDefaultMediaLibraryDiskDriver($default));
}

/**
 * @param string|null $default
 *
 * @return string
 */
function getDefaultMediaLibraryConversionsDiskDriver($default = 'local'): string
{
    return trim(config('nova-media-library.conversions_disk', getDefaultMediaLibraryDiskDriver($default)));
}

/**
 * @param string|null $default
 *
 * @return \Illuminate\Filesystem\FilesystemAdapter
 */
function getDefaultMediaLibraryConversionsDiskStorage($default = 'local'): FilesystemAdapter
{
    return \Storage::disk(getDefaultMediaLibraryConversionsDiskDriver($default));
}

if( !function_exists('array_only_except') ) {
    /**
     * Get two arrays, one has the second argument, and another one without it
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    function array_only_except($array, $keys): array
    {
        return [
            array_only($array, $keys),
            array_except($array, $keys),
        ];
    }
}

if( !function_exists('array_except_only') ) {
    /**
     * Get two arrays, one without the second argument, and another one with it
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    function array_except_only($array, $keys): array
    {
        return [
            array_except($array, $keys),
            array_only($array, $keys),
        ];
    }
}

if( !function_exists('guessPermissionName') ) {
    /**
     * Returns prefix of permissions name
     *
     * @param \Illuminate\Routing\Controller|string|null $controller      Controller or controller name, default: {@see currentController()}
     * @param string|null                                $permission_name Permission name
     * @param string                                     $separator       Permission name separator
     *
     * @return string
     */
    function guessPermissionName($controller = null, $permission_name = null, $separator = "."): string
    {
        $permission_name ??= currentAction();

        $controller = $controller instanceof \Illuminate\Routing\Controller ? get_class($controller) : ($controller ? trim($controller) : currentControllerClass(false));

        $controller = str_before(class_basename($controller), "Controller");

        $controller = snake_case($controller);

        $controller .= '#' . ($permission_name ? snake_case($permission_name) : '');

        return str_ireplace("#", $separator, trim($controller, "#"));
    }
}

if( !function_exists('isPermissionExists') ) {
    /**
     * @param string $permission_name
     *
     * @return bool
     */
    function isPermissionExists(string $permission_name): bool
    {
        $permission = toCollect(config('permission.permissions'))
            ->first(
                fn($permission) => data_get($permission, 'name', '') === $permission_name
            );

        return !empty($permission);
    }
}
