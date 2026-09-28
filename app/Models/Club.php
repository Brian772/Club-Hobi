<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    protected $fillable = [
        'id',
        'name',
        'hobby_id',
        'description',
        'created_by',
        'cover_url',
    ];

    public function getCoverDisplayUrlAttribute(): string
    {
        if (empty($this->cover_url)) {
            return 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80';
        }
        if (str_starts_with($this->cover_url, 'http://') || str_starts_with($this->cover_url, 'https://')) {
            return $this->cover_url;
        }
        return \Illuminate\Support\Facades\Storage::url($this->cover_url);
    }

    public function hobby()
    {
        return $this->belongsTo(Hobby::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function members(): HasMany
    {
        return $this->hasMany(ClubMember::class, 'club_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'club_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ClubFiles::class, 'club_id');
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(ClubJoinRequest::class, 'club_id');
    }
}