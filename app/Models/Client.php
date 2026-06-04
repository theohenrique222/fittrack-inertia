<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function personal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class);
    }

    public function bodyMeasurements(): HasMany
    {
        return $this->hasMany(BodyMeasurement::class);
    }

    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }
}
