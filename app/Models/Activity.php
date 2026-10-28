<?php

namespace App\Models;

use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activity extends Model
{
    protected $fillable = [
        'code',
        'name',
        'activity_type_id',
        'description',
        'target_participants',
        'created_by',
        'person_in_charge_id',
        'status',
    ];

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function personInCharge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'person_in_charge_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ActivityAssignment::class);
    }

    public function activityParticipants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(
            Participant::class,
            'activity_participants'
        )->withPivot('id', 'registered_at');
    }

    public function documentations(): HasMany
    {
        return $this->hasMany(Documentation::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }
}