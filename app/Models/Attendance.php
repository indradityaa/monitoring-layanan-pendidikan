<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'activity_participant_id',
    'status',
    'attendance_time',
    'note',
])]
class Attendance extends Model
{
    protected $casts = [
        'attendance_time' => 'datetime',
    ];

    public function activityParticipant(): BelongsTo
    {
        return $this->belongsTo(ActivityParticipant::class);
    }
}