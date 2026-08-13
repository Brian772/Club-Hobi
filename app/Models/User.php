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
        'password_hash',
        'avatar_url',
        'bio',
        'role_global',
        'status',
        'suspended_until',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'suspended_until' => 'datetime',
    ];

    // Wajib di-override karena kolom password Anda bernama password_hash
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}