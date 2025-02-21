<?php

use App\Models\Permission;
use Illuminate\Support\Collection;

/*
 * Helpers Examples
 * [
 *     'permissionByName' => permissionByName("viewUser")->name,
 *     'permissionsForNames' => permissionsForNames("view")->take(2)->map->name->all(),
 *
 *     'permissionsByGroup' => permissionsByGroup("User")->take(2)->map->name->all(),
 *     'permissionsForGroups' => permissionsForGroups("User")->take(2)->map->name->all(),
 *
 *     'permissions' => permissions("view", "User")->take(2)->map->name->all(),
 *     'permissionQuery' => $q=permissionQuery(["viewUser", 'createUser'])->get()->take(2)->map->name->all(),
 * ] = [
 *   "permissionByName" => "viewUser",
 *   "permissionsForNames" => [
 *     "viewClient",
 *     "viewMaterial",
 *   ],
 *   "permissionsByGroup" => [
 *     "viewAnyUser",
 *     "viewUser",
 *   ],
 *   "permissionsForGroups" => [
 *     "viewAnyUser",
 *     "viewUser",
 *   ],
 *   "permissions" => [
 *     "viewClient",
 *     "viewMaterial",
 *   ],
 *   "permissionQuery" => [
 *     "createUser",
 *     "viewUser",
 *   ],
 * ]
 */

if( !function_exists('permissionByName') ) {
    /**
     * get permission by full name
     * Helper for \App\Models\Permission::scopeByName
     *
     * @param string $name
     *
     * @return \App\Models\Permission|null
     */
    function permissionByName(string $name): ?Permission
    {
        return with(Permission::byName($name)->first());
    }
}

if( !function_exists('permissionsByGroup') ) {
    /**
     * get permission by full group
     * Helper for \App\Models\Permission::scopeByGroup
     *
     * @param string $name
     *
     * @return \Illuminate\Support\Collection
     */
    function permissionsByGroup($name): Collection
    {
        return Permission::ByGroup($name)->get();
    }
}

if( !function_exists('permissionsForNames') ) {
    /**
     * get permissions by name prefix
     * Helper for \App\Models\Permission::forPermission
     *
     * @param array|string $name
     *
     * @return \Illuminate\Support\Collection
     */
    function permissionsForNames(...$name): Collection
    {
        return Permission::forPermission($name)->get();
    }
}

if( !function_exists('permissionsForGroups') ) {
    /**
     * get permission by name suffix
     * Helper for \App\Models\Permission::forGroups
     *
     * @param array|string $name
     *
     * @return \Illuminate\Support\Collection
     */
    function permissionsForGroups(...$name): Collection
    {
        return Permission::forGroups($name)->get();
    }
}

if( !function_exists('permissions') ) {
    /**
     * get permission by name prefix or suffix
     * Helper for \App\Models\Permission::for
     *
     * @param string|array $name
     *
     * @return \Illuminate\Support\Collection
     */
    function permissions(...$name): Collection
    {
        return Permission::for(...$name)->get();
    }
}

if( !function_exists('permissionQuery') ) {
    /**
     * get permission by name prefix or suffix
     * Helper for \App\Models\Permission::for
     *
     * @param string|array $name
     * @param array|null   $options values: bool startWith, bool endWith
     * @param string|array $other_names
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function permissionQuery(
        $name,
        ?array $options = null,
        ...$other_names
    ): \Illuminate\Database\Eloquent\Builder {
        $options = collect($options)
            ->mapWithKeys(fn($v, $k) => [ camel_case($k) => toBoolValue($v) ])
            ->mergeIfMissing([
                                 'startWith' => false,
                                 'endWith' => false,
                             ])
            ->toArray();

        return Permission::forPermissionQuery($options, [
            ...array_wrap($name),
            ...array_wrap($other_names),
        ]);
    }
}

if( !function_exists('guessPermissionNameViaController') ) {
    /**
     * @param string|\App\Http\Controllers\Controller|\App\Http\Controllers\Api\Controller|null $controller
     * @param string|null                                                                       $method
     *
     * @return string
     */
    function guessPermissionNameViaController($controller = null, $method = null): string
    {
        try {
            $method ??= currentActionName();
            $controller ??= class_basename(currentController());
        } catch(\Exception $exception) {
            return "";
        }

        [ $_class, $_method ] = explode("::", guessPermissionName($controller, $method, '::'));
        $model = last(guessModelsViaController($_class, $_class)) ?: $_class;
        $_method = currentRoute()->getController()->resourceAbilityMap()[ $_method ] ?? $_method;
        $permissionName = $_method . ' ' . class_basename($model);

        return camel_case($permissionName);
    }
}

if( !function_exists('beginForgetCachedPermissions') ) {
    function beginForgetCachedPermissions(): void
    {
        app()[ \Spatie\Permission\PermissionRegistrar::class ]->forgetCachedPermissions();
    }
}

if( !function_exists('finishForgetCachedPermissions') ) {
    function finishForgetCachedPermissions(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
