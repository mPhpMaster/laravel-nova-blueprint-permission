<?php

namespace App\Models;

/**
 */
class UserProfile extends User
{
    protected $table = 'users';

    /**
     * Returns translations file name.
     *
     * @return string|null
     */
    public static function getTranslationKey(): ?string
    {
        return 'user_profile';
    }
}
