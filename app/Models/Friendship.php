<?php

namespace App\Models;

use App\Enums\FriendshipStatus;
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
}
