<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;

class Like extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Like $like) {
            if (!Auth::check()) {
                return;
            }

            $like->user_id = Auth::user()->id;
        });
    }

    public function scopeUserLike(Builder $query, Post $post): Builder
    {
        return $query->where([
            ['user_id', Auth::user()->id],
            ['post_id', $post->id],
        ]);
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
