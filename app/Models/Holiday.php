<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'date',
        'is_recurring_weekly',
        'day_of_week',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring_weekly' => 'boolean',
    ];
}
