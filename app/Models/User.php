<?php

namespace App\Models;

use App\Models\Club;
use App\Models\Report;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        'password',
        'password_hash',
        'provider_name',
        'provider_id',
        'avatar_url',
        'bio',
        'interests',
        'role_global',
        'status',
        'reason',
        'suspended_until',
        'email_verified_at',
        'remember_token',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'password_hash',
    ];

    protected $casts = [
        'suspended_until' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
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

    public function isOnline(): bool
    {
        return $this->last_seen_at?->greaterThan(now()->subMinutes(2)) ?? false;
    }

    public function getAvatarFullUrlAttribute(): ?string
    {
        $avatar = trim((string) ($this->avatar_url ?? ''));

        if ($avatar === '') {
            return null;
        }

        if (filter_var($avatar, FILTER_VALIDATE_URL) !== false) {
            return $avatar;
        }

        if (!Storage::disk('public')->exists($avatar)) {
            return null;
        }

        return url('storage/' . ltrim($avatar, '/'));
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash ?? $this->password ?? '';
    }

    public function passwordMatches(string $plainPassword): bool
    {
        $storedPassword = $this->password_hash ?? $this->password ?? null;

        if (blank($storedPassword)) {
            return false;
        }

        try {
            if (Hash::check($plainPassword, $storedPassword)) {
                return true;
            }
        } catch (\RuntimeException $e) {
            // Allow legacy or manually inserted values that are not BCrypt hashes.
        }

        return hash_equals((string) $storedPassword, $plainPassword);
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

        return collect(explode(',',  $this->interests))
            ->map(fn($item) => trim($item))
            ->filter()
            ->values()
            ->toArray();
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

    public function clubRequests()
    {
        return $this->hasMany(ClubRequest::class, 'user_id');
    }

    public function reviewedClubRequests()
    {
        return $this->hasMany(ClubRequest::class, 'reviewed_by');
    }

    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class, 'user_id');
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportedRecived()
    {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    public function appeal()
    {
        return $this->hasMany(Appeal::class, 'user_id');
    }
    
}
