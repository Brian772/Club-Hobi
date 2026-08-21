<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'interests',
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

    protected $appends = ['avatar_full_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = Str::uuid();
            }
        });
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest('created_at');
    }

    public function getAvatarFullUrlAttribute(): ?string
    {
        $avatar = (string) ($this->avatar_url ?? '');
        if (!$avatar === '') {
            return null;
        }

        if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://')) {
            return $this->avatar_url;
        }

        return Storage::disk('public')->url($this->avatar_url);
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash ?? $this->password ?? '';
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password_hash'] = $value;
    }

    public function getInterestArrayAttribute(): array
    {
        if (empty($this->interests)) {
            return [];
        }

        return collect(explode(', ',  $this->interests))
            ->map(fn($item) => trim($item))
            ->filter()
            ->values()
            ->toArray();
    }
}
