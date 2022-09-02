<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function __invoke(Request $request)
    {
        $user = User::firstWhere([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
        ]);

        $authors = collect([
            $user,
            ...$user->invitedFriends,
            ...$user->invitedByFriends,
        ]);

        $authors = User::find($authors->pluck('id'));

        $pagination = Post::query()
            ->with('author:id,first_name,last_name,profile_image,background_image')
            ->withCount([
                'likes',
                'comments' => fn (Builder $query) => $query->where('resource', 'POST'),
            ])
            ->withExists([
                'likes as is_liked' => fn (Builder $query) => $query->where('user_id', $user->id),
            ])
            ->whereBelongsTo($authors, 'author')
            ->whereDoesntHave('hidden', fn (Builder $query) => $query->where('user_id', $user->id))
            ->oldest('id')
            ->paginate(10, [
                'id',
                'content',
                'images',
                'author_id',
                'commenting',
                'created_at',
                'updated_at',
            ]);

        return $pagination;
    }
}
