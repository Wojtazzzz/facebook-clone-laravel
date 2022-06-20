<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostObserver
{
    public function deleted(Post $post): void
    {
        if (!$post->images) {
            return;
        }

        if (in_array('http', $post->images, true)) {
            return;
        }

        Storage::disk('public')->delete(...$post->images);
    }
}
