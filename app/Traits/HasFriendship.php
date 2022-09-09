<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\FriendshipStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Enumerable;

trait HasFriendship
{
    public function friendsOfMine(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', FriendshipStatus::CONFIRMED);
    }

    public function friendOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', FriendshipStatus::CONFIRMED);
    }

    public function getFriendsAttribute(): Collection
    {
        if (! array_key_exists('friends', $this->relations)) {
            $this->loadFriends();
        }

        return $this->getRelation('friends');
    }

    protected function loadFriends(): void
    {
        if (! array_key_exists('friends', $this->relations)) {
            $friends = $this->mergeFriends();

            $this->setRelation('friends', $friends);
        }
    }

    protected function mergeFriends(): Enumerable
    {
        return $this->friendsOfMine->merge($this->friendOf);
    }

    public function receivedInvites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', FriendshipStatus::PENDING);
    }

    public function sendedInvites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', FriendshipStatus::PENDING);
    }

    public function receivedBlocks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', FriendshipStatus::BLOCKED);
    }

    public function sendedBlocks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', FriendshipStatus::BLOCKED);
    }
}
