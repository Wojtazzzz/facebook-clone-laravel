<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'resource',
        'author_id',
        'resource_id'
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'resource_id', 'id');
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            if (!auth()->check()) return;

            $model->author_id = auth()->user()->id;
        });
    }
}
