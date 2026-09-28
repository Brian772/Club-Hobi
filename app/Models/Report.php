<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Report extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'reporter_id',
        'reported_user_id',
        'content_type',
        'content_id',
        'reason',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];


    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function content()
    {
        return $this->morphTo();
    }
}
