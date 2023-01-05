<?php /** @noinspection PhpMissingDocCommentInspection */

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
interface IUserType
{
    const NORMAL = "normal";
    const SUB_USER = "sub_user";
    const MERCHANT = "merchant";
    const MERCHANT_SUB_USER = "merchant_sub_user";

    public static function getAllUserTypes(): array;

    public function isUserTypeNormal(): bool;

    public function isUserTypeSubUser(): bool;

    public function isUserTypeMerchant(): bool;

    public function isUserTypeMerchantSubUser(): bool;

    public function setUserTypeAsNormal(bool $save = true): static;

    public function setUserTypeAsSubUser(bool $save = true): static;

    public function setUserTypeAsMerchant(bool $save = true): static;

    public function setUserTypeAsMerchantSubUser(bool $save = true): static;

    /**
     * @param Builder         $builder
     * @param string|string[] $user_type
     *
     * @return Builder
     */
    public function scopeByUserType(Builder $builder, array|string $user_type): Builder;

    public function scopeOnlyNormal(Builder $builder): Builder;

    public function scopeOnlySubUser(Builder $builder): Builder;

    public function scopeOnlyMerchant(Builder $builder): Builder;

    public function scopeOnlyMerchantSubUser(Builder $builder): Builder;
}
