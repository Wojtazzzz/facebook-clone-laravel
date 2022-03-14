<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Prunable, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'profile_image',
        'background_image',
        'password'
    ];

    protected $hidden = [
        'password',
        'email',
        'created_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s'
    ];

    // friends relationships
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

    public function messages()
    {
        return $this->belongsToMany(
            User::class, 
            'messages',
            'sender_id',
            'receiver_id'
        );
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(10));
    }

    protected function pruning()
    {
        // delete data which user created...
    }
}
