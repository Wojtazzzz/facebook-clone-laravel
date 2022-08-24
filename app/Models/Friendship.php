<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FriendshipStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $cast = [
        'status' => FriendshipStatus::class,
    ];

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    public function scopeRelation(Builder $query, int $userId, int $friendId): Builder
    {
        return $query->where(function (Builder $query) use ($userId, $friendId) {
            $query->where([
                ['user_id', $userId],
                ['friend_id', $friendId],
            ])->orWhere([
                ['user_id', $friendId],
                ['friend_id', $userId],
            ]);
        });
    }
}
