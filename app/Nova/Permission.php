<?php

namespace App\Nova;

use App\Models\Permission as MODEL;
use App\Traits\TPermissionResource;
use App\Traits\TRoleResource;

/**
 *
 */
class Permission extends Resource
{
	use TRoleResource;

	public static int $priority = 0;

	/**
	 * The logical group associated with the resource.
	 *
	 * @var string
	 */
	public static $group = 'Administration';

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
    public static $displayInNavigation = false;

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('nova-spatie-permissions::lang.Permissions');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('nova-spatie-permissions::lang.Permission');
    }
}
