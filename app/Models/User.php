<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaritalStatus;
use App\Traits\HasFriendship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Searchable;
    use HasFriendship;

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

    #[SearchUsingPrefix(['first_name', 'last_name'])]
    public function toSearchableArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'user_id', 'id');
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
}
