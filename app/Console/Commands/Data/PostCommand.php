<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;

class PostCommand extends Command
{
    protected $signature = 'data:post {amount=1} {--A|author=} {--C|comments=}';

    protected $description = 'Create specific amount of posts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (! $this->checkAmount()) {
            return;
        }

        $amount = $this->argument('amount');

        $data = [];

        if ($this->checkAuthor()) {
            $data['author_id'] = $this->option('author');
        }

        $posts = Post::factory($amount)->create($data);

        $this->info('Post(s) created successfully.');

        if (! $this->checkComments()) {
            return;
        }

        $commentsAmount = $this->option('comments');

        $posts->each(function (Post $post) use ($commentsAmount) {
            Comment::factory($commentsAmount)->forPost()->create([
                'commentable_id' => $post->id,
            ]);
        });

        $this->info('Comment(s) created successfully.');
    }

    private function checkAmount(): bool
    {
        if ((int) $this->argument('amount') >= 1) {
            return true;
        }

        $this->error('Amount must be integer greater than 0.');

        return false;
    }

    private function checkAuthor(): bool
    {
        $author = $this->option('author');

        if ((bool) ! $author) {
            return false;
        }

        User::findOrFail($author);

        return true;
    }

    private function checkComments(): bool
    {
        $comments = $this->option('comments');

        if ((bool) ! $comments) {
            return false;
        }

        if ((int) $comments >= 1) {
            return true;
        }

        $this->error('Comments must be integer greater than 0.');

        return false;
    }
}
