<?php

namespace App\Traits;

use App\Interfaces\IUserType;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin IUserType
 */
trait THasUserType
{
    public function isUserTypeNormal(): bool
    {
        return $this->user_type === IUserType::NORMAL;
    }

    public function isUserTypeSubUser(): bool
    {
        return $this->user_type === IUserType::SUB_USER;
    }

    public function isUserTypeMerchant(): bool
    {
        return $this->user_type === IUserType::MERCHANT;
    }

    public function isUserTypeMerchantSubUser(): bool
    {
        return $this->user_type === IUserType::MERCHANT_SUB_USER;
    }

    public function setUserTypeAsNormal(bool $save = true): static
    {
        $this->user_type = IUserType::NORMAL;
        if( $save ) {
            $this->save();
            $this->refresh();
        }

        return $this;
    }

    public function setUserTypeAsSubUser(bool $save = true): static
    {
        $this->user_type = IUserType::SUB_USER;
        if( $save ) {
            $this->save();
            $this->refresh();
        }

        return $this;
    }

    public function setUserTypeAsMerchant(bool $save = true): static
    {
        $this->user_type = IUserType::MERCHANT;
        if( $save ) {
            $this->save();
            $this->refresh();
        }

        return $this;
    }

    public function setUserTypeAsMerchantSubUser(bool $save = true): static
    {
        $this->user_type = IUserType::MERCHANT_SUB_USER;
        if( $save ) {
            $this->save();
            $this->refresh();
        }

        return $this;
    }

    /**
     * @param Builder         $builder
     * @param string|string[] $user_type
     *
     * @return Builder
     */
    public function scopeByUserType(Builder $builder, array|string $user_type): Builder
    {
        return $builder->whereIn('user_type', array_wrap($user_type));
    }

    public function scopeOnlyNormal(Builder $builder): Builder
    {
        return $builder->byUserType(IUserType::NORMAL);
    }

    public function scopeOnlySubUser(Builder $builder): Builder
    {
        return $builder->byUserType(IUserType::SUB_USER);
    }

    public function scopeOnlyMerchant(Builder $builder): Builder
    {
        return $builder->byUserType(IUserType::MERCHANT);
    }

    public function scopeOnlyMerchantSubUser(Builder $builder): Builder
    {
        return $builder->byUserType(IUserType::MERCHANT_SUB_USER);
    }
}
