<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'finish_at',
        'type',
        'color',
        'user_id',
        'type',
        'created_at',
        'updated_at',
    ];

}
