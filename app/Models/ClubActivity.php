<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubActivity extends Model
{
    protected $table = 'club_activities';

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $fillable = [
        'id',
        'actor_id',
        'club_id',
        'action',
        'target_type',
        'target_id',
        'metadata',
    ];

    public $casts = [
        'metadata' => 'array',
    ];

    public function getFormatMetadataAttribute(): array
    {
        $meta = $this->metadata ?? [];

        return match ($this->action) {
            'Create Post' => [
                'Title' => $meta['title'] ?? 'N/A',
                'Content' => $meta['content'] ?? 'N/A',
            ],

            'Delete Post' => [
                'Title' => $meta['title'] ?? 'N/A',
                'Content' => $meta['content'] ?? 'N/A',
            ],

            'Accept Join Request' => [
                'Role' => $meta['role'] ?? 'N/A',
            ],

            'Reject Join Request' => [
                'Role' => $meta['role'] ?? 'N/A',
            ],

            'Update Club' => [
                'Name' => $meta['name'] ?? 'N/A',
                'Description' => $meta['description'] ?? 'N/A',
                'Cover Url' => $meta['cover_url'] ?? 'N/A',
            ],

            'Leave Club' => [
                'Role' => $meta['role'] ?? 'N/A',
            ],

            'Kick Member' => [
                'Kicked By' => $meta['kicked_by'] ?? 'N/A',
                'Role' => $meta['role'] ?? 'N/A',
            ],

            'Promote Moderator' => [
                'Promoted By' => $meta['promoted_by'] ?? 'N/A',
                'Previous Role' => $meta['previous_role'] ?? 'N/A',
                'New Role' => $meta['new_role'] ?? 'N/A',
            ],

            'Demote Moderator' => [
                'Demoted By' => $meta['demoted_by'] ?? 'N/A',
                'Previous Role' => $meta['previous_role'] ?? 'N/A',
                'New Role' => $meta['new_role'] ?? 'N/A',
            ],

            default => $meta,
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
