<?php

namespace App\Interfaces;

/**
 * @property static $permission_name string|null
 */
interface IControllerHasAuth
{
    /** @return string|null */
    public static function getPermissionName(): ?string;

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array of ['method' => 'policy method']
     */
    public static function getAbilitiesMap(): array;

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    public static function getMethodsWithoutModels(): array;
}
