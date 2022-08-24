<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function scopeNotHidden(Builder $query): Builder
    {
        return $query->whereDoesntHave('hidden', fn (Builder $query) => $query->where('user_id', Auth::user()->id));
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    // public function likes(): HasMany
    // {
    //     return $this->hasMany(Like::class, 'post_id', 'id');
    // }

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
