<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'images',
        'author_id',
        'commenting',
    ];

    protected $casts = [
        'images' => 'array',
        'commenting' => 'boolean',
    ];

    public function scopeWhichNotHidden(Builder $query): Builder
    {
        return $query->whereDoesntHave('hidden', fn (Builder $query) => $query->where('user_id', Auth::user()->id));
    }

    public function scopeWithIsLiked(Builder $query): Builder
    {
        return $query->withExists([
            'likes as is_liked' => fn (Builder $query) => $query->where('user_id', Auth::user()->id),
        ]);
    }

    public function scopeFromAuthors(Builder $query, Collection | User $users): Builder
    {
        return $query->whereBelongsTo($users, 'author');
    }

    public function scopeWithStats(Builder $query): Builder
    {
        return $query->withCount([
            'likes',
            'comments' => fn (Builder $query) => $query->where('resource', 'POST'),
        ]);
    }

    public function scopeWithAuthor(Builder $query): Builder
    {
        return $query->with('author:id,first_name,last_name,profile_image,background_image');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'resource_id', 'id');
    }

    public function hidden(): HasMany
    {
        return $this->hasMany(HiddenPost::class, 'post_id', 'id');
    }

    // Method 'App\Models\Post::saved()' is not compatible with method 'Illuminate\Database\Eloquent\Model::saved()',
    // so it's called stored
    public function stored(): HasMany
    {
        return $this->hasMany(SavedPost::class, 'post_id', 'id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
