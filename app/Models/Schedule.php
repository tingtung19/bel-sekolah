<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'time',
        'label',
        'days_of_week',
        'sound_id',
        'language',
        'is_active',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
    ];

    public function sound(): BelongsTo
    {
        return $this->belongsTo(Sound::class);
    }
}
