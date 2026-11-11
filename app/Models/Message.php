<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'club_id',
        'sender_id',
        'receiver_id',
        'content',
        'photo_path',
        'view_once',
        'viewed_at',
        'is_read',
        'send_at',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'view_once' => 'boolean',
        'send_at' => 'datetime',
        'viewed_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
