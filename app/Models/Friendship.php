<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friendship extends Model
{
    use HasFactory;

    protected $hidden = [
        'updated_at',
        'status'
    ];

    protected $with = [
        'inviter'
    ];

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_user', 'id');
    }
}
