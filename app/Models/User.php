<?php

namespace App\Models;

use App\Interfaces\IRole;
use App\Models\Scopes\Searchable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\FileAdder;
use Spatie\MediaLibrary\MediaCollections\FileAdderFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Storage;

/**
 * @mixin IdeHelperUser
 */
class User extends \App\Models\Abstracts\UserAbstract implements HasMedia
{
    use \Laravel\Nova\Auth\Impersonatable;
    use HasApiTokens, HasFactory, Notifiable;
    use InteractsWithMedia;
    use SoftDeletes;
    use Searchable;
    use HasRoles;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_admin',
        'is_super_admin',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'name' => 'string',
        'is_admin' => 'bool',
        'is_super_admin' => 'bool',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        // "roles",
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
     * WHAT IS THIS FOR?
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function role()
    {
        return $this->roles()->first();
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
        return $builder->where('email', value($email));
    }

    /**
     * @return string
     */
    public function getIsSuperAdminAttribute()
    {
        return $this->isSuperAdmin();
    }

    /**
     * @return string
     */
    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|null $user
     *
     * @return bool
     */
    public function isAdmin(\Illuminate\Database\Eloquent\Model|null $user = null): bool
    {
        /** @var $user \App\Models\User */
        $user ??= $this;

        return $user && $user->hasRole([/*IRole::SuperAdminRole,*/ IRole::AdminRole ]);
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(\Illuminate\Database\Eloquent\Model|null $user = null)
    {
        /** @var $user \App\Models\User */
        $user ??= $this;

        return $user && $user->hasRole(IRole::SuperAdminRole);
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->hasRole('user');
    }

    /**
     * @param \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('my-collection');

        $this->addMediaCollection('my-other-collection');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }

    /**
     * Add a file to the media library from a stream.
     *
     * @param $stream
     */
    public function addMediaFromStream($stream): FileAdder
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'media-library');

        file_put_contents($tmpFile, $stream);

        $file = app(FileAdderFactory::class)
            ->create($this, $tmpFile)
            ->usingFileName(basename($tmpFile) . '.png');

        return $file;
    }

    /**
     * Add a file to the media library that contains the given string.
     *
     * @param string string
     */
    public function addMediaFromString(string $text): FileAdder
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'media-library');

        file_put_contents($tmpFile, $text);

        $file = app(FileAdderFactory::class)
            ->create($this, $tmpFile)
            ->usingFileName(basename($tmpFile));

        return $file;
    }
}
