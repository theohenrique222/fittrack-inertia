<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseCustomWeight extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'client_id',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:1',
        ];
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
