<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'muscle_group',
        'category_id',
        'equipment',
        'difficulty',
        'instructions',
        'video_url',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function workouts(): BelongsToMany
    {
        return $this->belongsToMany(Workout::class, 'exercise_workout')
            ->using(WorkoutExercise::class)
            ->withPivot('sets', 'reps', 'rest_seconds', 'weight', 'order', 'notes')
            ->withTimestamps();
    }

    public function customWeights(): HasMany
    {
        return $this->hasMany(ExerciseCustomWeight::class);
    }
}
