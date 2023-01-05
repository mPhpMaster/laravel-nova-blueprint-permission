<?php

namespace App\Nova;

use App\Models\Role as MODEL;
use App\Traits\TRoleResource;

/**
 *
 */
class Role extends Resource
{
    use TRoleResource;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = MODEL::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = true;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('nova-spatie-permissions::lang.Roles');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('nova-spatie-permissions::lang.Role');
    }
}
