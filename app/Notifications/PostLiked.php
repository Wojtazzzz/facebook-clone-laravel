<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification
{
    use Queueable;

    private int $friendId;

    private Post $post;

    public function __construct(int $friendId, Post $post)
    {
        $this->friendId = $friendId;
        $this->post = $post;
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
            'postId' => $this->post->id,
        ];
    }

    // $notifiable === post author
    public function shouldSend($notifiable)
    {
        $isExist = $notifiable->notifications()->where([
            ['data->postId', $this->post->id],
            ['data->friendId', $this->friendId],
        ])->exists();

        return ! $isExist;
    }
}
