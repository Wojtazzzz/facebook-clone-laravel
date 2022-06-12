<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'profile_image',
        'background_image',
        'password',
    ];

    protected $hidden = [
        'password',
        'email',
        'created_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function lastConversationMessage(): Attribute
    {
        return new Attribute(function () {
            if ($this->messages[0]->pivot->created_at > $this->theMessages[0]->pivot->created_at) {
                return $this->messages[0];
            } else {
                return $this->theMessages[0];
            }
        });
    }

    public function pokedUsers(): HasMany
    {
        return $this->hasMany(Poke::class, 'initiator_id', 'id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id', 'id');
    }

    public function sendedMessages(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'messages', 'sender_id', 'receiver_id')
            ->withPivot(['id', 'text', 'created_at']);
    }

    public function receivedMessages(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'messages', 'receiver_id', 'sender_id')
            ->withPivot(['id', 'text', 'created_at']);
    }

    public function invitedFriends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'CONFIRMED');
    }

    public function invitedByFriends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'CONFIRMED');
    }

    public function receivedInvites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'PENDING');
    }

    public function sendedInvites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'PENDING');
    }

    public function receivedBlocks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
            ->wherePivot('status', 'BLOCKED');
    }

    public function sendedBlocks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
            ->wherePivot('status', 'BLOCKED');
    }
}
