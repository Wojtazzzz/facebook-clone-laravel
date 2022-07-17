<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MaritalStatus;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_image',
        'background_image',
        'works_at',
        'went_to',
        'lives_in',
        'from',
        'marital_status',
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
        'marital_statsu' => MaritalStatus::class,
    ];

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
