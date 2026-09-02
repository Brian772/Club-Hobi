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

    protected $fillable = [
        'id',
        'name',
        'description',
        'cover_url',
        'category',
    ];

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
}
