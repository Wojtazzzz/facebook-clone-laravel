<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaritalStatus;
use App\Traits\HasFriendship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
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
        'born_at',
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

    protected $dates = [
        'born_at',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => "{$attributes['first_name']} {$attributes['last_name']}",
        );
    }

    public function scopeSearchByName(Builder $query, $search): Builder
    {
        return $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id', 'id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'id');
    }
}
