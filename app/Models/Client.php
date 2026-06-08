<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nickname',
        'plan_id',
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

    public function customWeights(): HasMany
    {
        return $this->hasMany(ExerciseCustomWeight::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function changePlan(?Plan $plan): void
    {
        $this->plan()->associate($plan);
        $this->save();
    }
}
