<?php

namespace App\Models;

use App\Models\Club;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasUuids, Notifiable, MustVerifyEmailTrait;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password_hash',
        'avatar_url',
        'bio',
        'role_global',
        'status',
        'suspended_until',
        'email_verified_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'suspended_until' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function clubs()
{
    return $this->belongsToMany(
        Club::class,
        'club_members',
        'user_id',
        'club_id'
        );
    }
}