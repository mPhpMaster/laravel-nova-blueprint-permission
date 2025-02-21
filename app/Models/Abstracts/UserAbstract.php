<?php

namespace App\Models\Abstracts;

use App\Interfaces\IUserType;
use App\Traits\THasUserType;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 *
 */
class UserAbstract extends \App\Models\Abstracts\Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
//    \Illuminate\Contracts\Auth\MustVerifyEmail,
    HasLocalePreference,
    IUserType
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use THasUserType;
    use TwoFactorAuthenticatable;

    /**
     * @param $value
     *
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes[ 'password' ] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Get the preferred locale of the entity.
     *
     * @return string|null
     */
    public function preferredLocale()
    {
        return currentLocale();
    }

    public static function getAllUserTypes(): array
    {
        return [
            IUserType::NORMAL,
            IUserType::SUB_USER,
            IUserType::MERCHANT,
            IUserType::MERCHANT_SUB_USER,
        ];
    }
}
