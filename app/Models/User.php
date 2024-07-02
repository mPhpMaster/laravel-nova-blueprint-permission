<?php

namespace App\Models;

use App\Interfaces\IRole;
use App\Models\Scopes\Searchable;
use App\Traits\THasImage;
use App\Traits\THasRoles;
use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends \App\Models\Abstracts\UserAbstract implements \App\Interfaces\IHasPermissionGroup
{
	use \Laravel\Nova\Auth\Impersonatable;
	use Notifiable;
	use HasFactory;
	use Searchable;
	use SoftDeletes;
	use HasApiTokens;
	use TModelTranslation;
	use THasRoles;
	use THasImage;
	use \App\Traits\THasPermissionGroup;

    /** @type string */
    public const PERMISSION = 'User';
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'role',
//        'image_url',
    ];

    /**
     * @var string[]
     */
    protected $searchableFields = [ '*' ];

    /**
     * @var string[]
     */
    protected $hidden = [ 'password', 'remember_token' ];


    /**
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
        'image' => 'string',
//        'image_url' => 'string',
    ];

    /**
     * @param $value
     *
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->email, config('auth.super_admins')) ||
            $this->hasAnyRole(IRole::SuperAdminRole);
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin();
    }

    public function isAnyAdmin(): bool
    {
        return $this->isAdmin() /** || $this->isSuperAdmin()  */;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param                                       $email
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail(Builder $builder, $email): Builder
    {
        return $builder->whereIn('email', array_wrap(value($email)));
    }

    /**
     * @param \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
    }
}
