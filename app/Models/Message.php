<?php

namespace App\Models;

use App\Events\ChatMessageSended;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'sender_id',
        'receiver_id'
    ];

    protected $dispatchesEvents = [
        'created' => ChatMessageSended::class
    ];
}
