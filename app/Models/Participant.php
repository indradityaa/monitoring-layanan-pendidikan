<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'name',
    'institution',
    'position',
])]
class Participant extends Model
{
    public function activityParticipants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(
            Activity::class,
            'activity_participants'
        )->withPivot('id', 'registered_at');
    }
}