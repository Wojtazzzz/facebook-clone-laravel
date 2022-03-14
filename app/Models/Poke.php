<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Poke extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'poked_id',
        'last_poked_id',
        'count'
    ];

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d h:i:s'
    ];

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id', 'id');
    } 
}
