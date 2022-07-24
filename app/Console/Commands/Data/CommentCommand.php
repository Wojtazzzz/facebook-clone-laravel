<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;

class CommentCommand extends Command
{
    protected $signature = 'data:comment {post} {amount=1} {--A|author=}';
    protected $description = 'Create comments to specific post';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $post = Post::findOrFail($this->argument('post'));

        if (!$this->checkAmount()) {
            return;
        }

        $data = [];

        if ($this->checkAuthor()) {
            $data['author_id'] = $this->option('author');
        }

        $amount = $this->argument('amount');

        Comment::factory($amount)->create([
            'resource_id' => $post->id,
        ] + $data);

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

        if ((bool) !$author) {
            return false;
        }

        User::findOrFail($author);

        return true;
    }
}
