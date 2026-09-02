<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Hobby extends Model
{
    protected $fillable = [
        'name',
    ];

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class, 'hobby_id');
    }

    public function clubRequest(): HasMany
    {
        return $this->hasMany(ClubRequest::class, 'hobby_id');
    }

}
