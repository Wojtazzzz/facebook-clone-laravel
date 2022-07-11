<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'resource',
        'author_id',
        'resource_id',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'resource_id', 'id');
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Comment $comment) {
            if (!Auth::check()) {
                return;
            }

            if (isset($comment->author_id)) {
                return;
            }

            $comment->author_id = Auth::user()->id;
        });
    }
}
