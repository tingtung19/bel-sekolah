<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BellLog extends Model
{
    protected $fillable = [
        'triggered_at',
        'source_type',
        'label',
        'sound_name',
        'language',
        'status',
        'notes',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
    ];
}
