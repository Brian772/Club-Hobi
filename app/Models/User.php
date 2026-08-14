<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    // Primary key bertipe string (uuid), bukan auto-increment
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'password_hash',
        'provider_name',
        'provider_id',
        'avatar_url',
        'bio',
        'role_global',
        'status',
        'suspended_until',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'password_hash',
    ];

    protected $casts = [
        'suspended_until' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash ?? $this->password ?? '';
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = $value;
        $this->attributes['password_hash'] = $value;
    }
}