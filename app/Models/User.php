<?php

namespace App\Models;


use App\Interfaces\IRole;
use App\Models\Scopes\Searchable;
use App\Traits\THasImage;
use App\Traits\THasRoles;
use App\Traits\TModelTranslation;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends \App\Models\Abstracts\UserAbstract implements \App\Interfaces\IHasPermissionGroup
	// ,HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

	/**
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
		'email_verified_at',
	];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
	        'email_verified_at' => 'datetime',
	        'created_at'        => 'datetime',
	        'updated_at'        => 'datetime',
	        'deleted_at'        => 'datetime',
	        'role'              => 'string',
	        'image'             => 'string',
	        //        'image_url' => 'string',
            'password' => 'hashed',
            'name'           => 'string',
            'is_admin'       => 'bool',
            'is_super_admin' => 'bool',
        ];
    }

	/**
	 * WHAT IS THIS FOR?
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function role()
	{
		return $this->roles()->first();
	}

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
		return $this->isAdmin()/** || $this->isSuperAdmin()  */ ;
	}

	/**
	 * Send the email verification notification.
	 *
	 * @return void
	 */
	public function sendEmailVerificationNotification()
	{
		$this->notify(new VerifyEmail());
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

	public function canViewAny($arguments = []): bool
	{
		$arguments = collect($arguments)
			->map(fn($argument) => 'viewAny'.class_basename($argument))
			->toArray();
//dd($arguments, Permission::forPermission($arguments)->getSql());
		return $this->hasAnyPermission(
			Permission::forPermission($arguments)->first(),
		);
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
