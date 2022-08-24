<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Notification as EloquentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PostLiked extends Notification
{
    use Queueable;

    private int $friendId;

    private int $postId;

    public function __construct(int $friendId, int $postId)
    {
        $this->friendId = $friendId;
        $this->postId = $postId;
    }

    public function via(): array
    {
        return ['database'];
    }

    public function toArray(): array
    {
        return [
            'friendId' => $this->friendId,
            'message' => 'Liked your post',
            'link' => "/profile/$this->friendId",
            'postId' => $this->postId,
        ];
    }

    public function shouldSend($notifiable, $channel)
    {
        $isPostAuthor = $notifiable->id === Auth::user()->id;

        $isExist = EloquentNotification::where([
            ['data->postId', $this->postId],
            ['data->friendId', $this->friendId],
        ])->exists();

        return ! $isPostAuthor && ! $isExist;
    }
}
