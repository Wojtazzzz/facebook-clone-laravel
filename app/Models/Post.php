<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Post $post) {
            if (!Auth::check()) {
                return;
            }

            if (isset($post->author_id)) {
                return;
            }

            $post->author_id = Auth::user()->id;
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'post_id', 'id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'resource_id', 'id');
    }

    public function hidden(): HasMany
    {
        return $this->hasMany(HiddenPost::class, 'post_id', 'id');
    }
}
