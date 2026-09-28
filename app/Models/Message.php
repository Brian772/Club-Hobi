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
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'send_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'send_at' => 'datetime',
    ];
}
