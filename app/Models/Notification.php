<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasUuids;

    protected $table = 'notifications';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'source_id',
        'is_read',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public static function createForUser(string $userId, string $title, string $content, string $type, ?string $sourceId = null): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'type' => $type,
            'source_id' => $sourceId,
            'is_read' => false,
        ]);
    }

    protected static function boot() {
        parent::boot();

        static::creating(function ($notification) {
            $notification->created_at = now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
