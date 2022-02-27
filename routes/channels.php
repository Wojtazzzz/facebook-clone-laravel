<?php

use App\Broadcasting\ChatMessageChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{text}.{receiverId}', ChatMessageChannel::class);
