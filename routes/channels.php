<?php

declare(strict_types=1);

use App\Broadcasting\ChatMessageChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{senderId}.{receiverId}', ChatMessageChannel::class);
