<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'activity_id',
        'schedule_date',
        'start_time',
        'end_time',
        'location_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}