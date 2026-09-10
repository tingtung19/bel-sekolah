<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialSchedule extends Model
{
    protected $fillable = [
        'date',
        'time',
        'label',
        'sound_id',
        'language',
        'overrides_regular',
    ];

    protected $casts = [
        'date' => 'date',
        'overrides_regular' => 'boolean',
    ];

    public function sound(): BelongsTo
    {
        return $this->belongsTo(Sound::class);
    }
}
