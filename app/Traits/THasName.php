<?php

namespace App\Traits;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait THasName
{
	public static function getNameAttributeName(): string
	{
		return 'name';
	}

	/**
	 * @return string|null
	 */
    public function getNameAttribute(): string|null
    {
        return $this->attributes[ columnLocalize(static::getNameAttributeName()) ] ?? '';
    }
}
