<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CommentLiked extends Notification
{
    use Queueable;

    private int $friendId;

    private int $commentId;

    public function __construct(int $friendId, int $commentId)
    {
        $this->friendId = $friendId;
        $this->commentId = $commentId;
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
            'commentId' => $this->commentId,
        ];
    }

    public function shouldSend($notifiable): bool
    {
        $isCommentAuthor = $notifiable->id === Auth::user()->id;

        $isExist = $notifiable->notifications()->where([
            ['data->commentId', $this->commentId],
            ['data->friendId', $this->friendId],
        ])->exists();

        return ! $isCommentAuthor && ! $isExist;
    }
}
