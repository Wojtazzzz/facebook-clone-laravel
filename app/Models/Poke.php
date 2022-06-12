<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Poke extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'latest_initiator_id',
        'count',
    ];

    public function scopePoke(Builder $query, int $userId, int $friendId): Builder
    {
        return $query
            ->where([
                ['user_id', $userId],
                ['friend_id', $friendId],
            ])
            ->orWhere([
                ['user_id', $friendId],
                ['friend_id', $userId],
            ]);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'latest_initiator_id', 'id');
    }
}
