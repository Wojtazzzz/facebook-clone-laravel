<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poke extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'poked_id',
        'last_poked_id',
        'count'
    ];
}
