<?php

namespace App\Models;

use App\Models\Report;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable
{
    public function createdActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    public function responsibleActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'person_in_charge_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ActivityAssignment::class);
    }

    public function uploadedDocumentations(): HasMany
    {
        return $this->hasMany(Documentation::class, 'uploaded_by');
    }

    public function submittedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'submitted_by');
    }

    public function validatedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'validated_by');
    }

    /**
     * @use HasFactory<UserFactory>
     */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}