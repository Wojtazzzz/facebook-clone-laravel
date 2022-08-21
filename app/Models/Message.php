<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\ChatMessageSent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'sender_id',
        'receiver_id',
        'status',
    ];

    protected $dispatchesEvents = [
        'created' => ChatMessageSent::class,
    ];

    protected $dates = [
        'read_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Message $message) {
            if (!Auth::check()) {
                return;
            }

            if (isset($message->sender_id)) {
                return;
            }

            $message->sender_id = Auth::user()->id;
        });
    }

    public function scopeConversation(Builder $query, int $userId, int $friendId): Builder
    {
        return $query->where([
            ['sender_id', $userId],
            ['receiver_id', $friendId],
        ])->orWhere([
            ['sender_id', $friendId],
            ['receiver_id', $userId],
        ]);
    }
}
