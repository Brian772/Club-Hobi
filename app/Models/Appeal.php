<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    use HasUuids;

    protected $fillable = [
        'id',
        'user_id',
        'reason',
        'status',
        'admin_note',
    ];
}
