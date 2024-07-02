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
	 * Indicates if the resource should be globally searchable.
	 *
	 * @var bool
	 */
	public static $globallySearchable = false;

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
	// public static $displayInNavigation = false;

	public function subtitle(): int|string|null
	{
		return null;
	}

	/**
	 * Get the displayable label of the resource.
	 *
	 * @return string
	 */
	public static function label()
	{
		return static::trans('plural');
	}

	/**
	 * Get the displayable singular label of the resource.
	 *
	 * @return string
	 */
	public static function singularLabel()
	{
		return static::trans('singular');
	}

	/**
	 * @return bool
	 */
	public static function isDisplayInNavigation(): bool
	{
		return isSuperAdmin() || isAdmin() || self::$displayInNavigation;
	}
}