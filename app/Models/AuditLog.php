<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
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
            'Ignored Report' => [
                'Reason' => $meta['reason'] ?? 'N/A',
            ],

            'Resolve Ban Report' => [
                'Action Taken' => $meta['action_taken'] ?? 'N/A',
                'Reason' => $meta['reason'] ?? 'N/A',
            ],

            'Resolve Suspend Report' => [
                'Action Taken' => $meta['action_taken'] ?? 'N/A',
                'Duration' => $meta['duration'] ?? 'N/A',
                'Reason' => $meta['reason'] ?? 'N/A',
            ],

            'Appeal Request' => [
                'Reason' => $meta['reason'] ?? 'N/A',
            ],

            'Accept Appeal Request' => [
                'Previous Status' => $meta['previous_status'] ?? 'N/A',
                'New Status' => $meta['new_status'] ?? 'N/A',
            ],

            'Reject Appeal Request' => [
                'Reason' => $meta['reason'] ?? 'N/A',
            ],

            'Reject Club Request' => [
                'Reason' => $meta['reason'] ?? 'N/A',
                'Requester ID' => $meta['requester_id'] ?? 'N/A',
            ],

            'Accept Club Request' => [
                'Club ID' => $meta['club_id'] ?? 'N/A',
                'Requester ID' => $meta['requester_id'] ?? 'N/A',
                'Result' => $meta['result'] ?? 'N/A',
            ],

            'Delete Club' => [
                'Club Name' => $meta['club_name'] ?? 'N/A',
            ],

            'Suspend User' => [
                'Reason' => $meta['reason'] ?? 'N/A',
                'Suspend Until' => $meta['suspend_until'] ?? 'N/A',
                'Previous Status' => $meta['previous_status'] ?? 'N/A',
                'New Status' => $meta['new_status'] ?? 'N/A',
            ],

            'Ban User' => [
                'Reason' => $meta['reason'] ?? 'N/A',
                'Previous Status' => $meta['previous_status'] ?? 'N/A',
                'New Status' => $meta['new_status'] ?? 'N/A',
            ],

            'Unsuspend User' => [
                'Previous Status' => $meta['previous_status'] ?? 'N/A',
                'New Status' => $meta['new_status'] ?? 'N/A',
            ],

            'Unban User' => [
                'Previous Status' => $meta['previous_status'] ?? 'N/A',
                'New Status' => $meta['new_status'] ?? 'N/A',
            ],

            'Add Hobby' => [
                'Hobby Name' => $meta['name'] ?? 'N/A',
            ],

            'Delete Hobby' => [
                'Hobby Name' => $meta['name'] ?? 'N/A',
            ],

            default => $meta,
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
