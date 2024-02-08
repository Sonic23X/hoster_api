<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    const TYPE_SUPER_ADMIN = 'superadmin';
    const TYPE_PARTNER = 'partner';
    const TYPE_USER = 'user';

    protected $guard_name = 'web';

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = $model->uuid ?: (string) Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'last_name',
        'second_last_name',
        'email',
        'telephone',
        'password',
        'is_partner',
        'user_by_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_partner' => 'boolean',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'roles',
        'properties'
    ];

    /**
     * Get all of the properties for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }
}
