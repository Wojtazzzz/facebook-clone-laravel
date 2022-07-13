<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SavedPost extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (SavedPost $savedPost) {
            if (!Auth::check()) {
                return;
            }

            if (isset($savedPost->user_id)) {
                return;
            }

            $savedPost->user_id = Auth::user()->id;
        });
    }
}
