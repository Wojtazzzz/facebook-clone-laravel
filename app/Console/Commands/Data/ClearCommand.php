<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Comment;
use App\Models\Friendship;
use App\Models\HiddenPost;
use App\Models\Like;
use App\Models\Message;
use App\Models\Poke;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearCommand extends Command
{
    protected $signature = 'data:clear';

    protected $description = 'Clear all records from database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        Comment::truncate();
        Friendship::truncate();
        HiddenPost::truncate();
        Like::truncate();
        Message::truncate();
        DB::table('notifications')->truncate();
        Poke::truncate();
        Post::truncate();
        SavedPost::truncate();
        User::truncate();

        $this->info('Database cleared successfully.');

        return 0;
    }
}
