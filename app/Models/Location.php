<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'address',
        'district',
        'city',
        'is_active',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}