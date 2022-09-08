<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostObserver
{
    public function deleted(Post $post): void
    {
        $this->deleteImages($post);
        $this->deleteFromHiddenPostsTable($post);
        $this->deleteFromSavedPostsTable($post);
    }

    public function updated(Post $post): void
    {
        if ((bool) $post->content) {
            return;
        }

        if ((bool) count($post->images)) {
            return;
        }

        $post->delete();
    }

    private function deleteImages(Post $post): void
    {
        if (! $post->images) {
            return;
        }

        if (in_array('http', $post->images, true)) {
            return;
        }

        Storage::disk('public')->delete(...$post->images);
    }

    private function deleteFromHiddenPostsTable(Post $post): void
    {
        $post->hidden()->delete();
    }

    private function deleteFromSavedPostsTable(Post $post): void
    {
        $post->stored()->delete();
    }
}
