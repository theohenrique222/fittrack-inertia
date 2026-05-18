<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkoutExercise extends Pivot
{
    protected $table = 'exercise_workout';

    protected $fillable = [
        'workout_id',
        'exercise_id',
        'sets',
        'reps',
        'rest_seconds',
        'order',
        'notes',
    ];

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
