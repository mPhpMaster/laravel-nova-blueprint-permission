<?php

namespace App\Interfaces;
/**
 * @property string $permission_group_name
 */
interface IHasPermissionGroup
{
    // public static string $permission_group_name;

    public static function getPermissionGroupName(): string;
}
