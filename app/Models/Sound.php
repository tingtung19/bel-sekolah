<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sound extends Model
{
    protected $fillable = [
        'name',
        'file_path',
        'language',
        'duration_sec',
        'is_system_default',
    ];

    protected $casts = [
        'is_system_default' => 'boolean',
        'duration_sec' => 'integer',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function specialSchedules(): HasMany
    {
        return $this->hasMany(SpecialSchedule::class);
    }

    public function getAssetUrlAttribute(): string
    {
        return asset($this->file_path);
    }
}
