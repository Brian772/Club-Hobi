<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'hobby_id',
        'cover_url',
        'reason',
        'status',
        'rejected_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function requester() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hobby() {
        return $this->belongsTo(Hobby::class);
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
