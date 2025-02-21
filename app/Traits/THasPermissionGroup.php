<?php

namespace App\Traits;

/**
 * @mixin \App\Interfaces\IHasPermissionGroup
 */
trait THasPermissionGroup
{
    public static function getPermissionGroupName(): string
    {
        return property_exists(static::class, 'permission_group_name') ?
            static::$permission_group_name :
            getConst([ static::class, 'PERMISSION' ], class_basename(static::class));
    }
}
