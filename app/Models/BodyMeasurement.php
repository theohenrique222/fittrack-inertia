<?php

namespace App\Models;

use App\Enums\ActivityLevel;
use App\Enums\Goal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'recorded_at',
        'weight',
        'height',
        'neck',
        'waist',
        'hip',
        'chest',
        'thigh',
        'arm',
        'forearm',
        'calf',
        'shoulders',
        'activity_level',
        'goal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'neck' => 'decimal:2',
            'waist' => 'decimal:2',
            'hip' => 'decimal:2',
            'chest' => 'decimal:2',
            'thigh' => 'decimal:2',
            'arm' => 'decimal:2',
            'forearm' => 'decimal:2',
            'calf' => 'decimal:2',
            'shoulders' => 'decimal:2',
            'activity_level' => ActivityLevel::class,
            'goal' => Goal::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
