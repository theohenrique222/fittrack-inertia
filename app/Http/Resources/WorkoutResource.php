<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
    private ?int $sessionId = null;

    public function withSessionId(?int $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client->id,
                'name' => $this->client->user->name,
                'nickname' => $this->client->nickname,
            ]),
            'client_id' => $this->client_id,
            'trainer' => $this->whenLoaded('trainer', fn () => [
                'id' => $this->trainer->id,
                'name' => $this->trainer->name,
            ]),
            'trainer_id' => $this->trainer_id,
            'exercises' => $this->whenLoaded('exercises', fn () => $this->exercises->map(fn ($exercise) => [
                'id' => $exercise->id,
                'name' => $exercise->name,
                'slug' => $exercise->slug,
                'description' => $exercise->description,
                'muscle_group' => $exercise->muscle_group,
                'equipment' => $exercise->equipment,
                'difficulty' => $exercise->difficulty,
                'instructions' => $exercise->instructions,
                'image' => $exercise->image,
                'video_url' => $exercise->video_url,
                'category' => $exercise->category ? [
                    'id' => $exercise->category->id,
                    'name' => $exercise->category->name,
                ] : null,
                'pivot' => [
                    'sets' => $exercise->pivot->sets,
                    'reps' => $exercise->pivot->reps,
                    'rest_seconds' => $exercise->pivot->rest_seconds,
                    'weight' => $exercise->pivot->weight ? (float) $exercise->pivot->weight : null,
                    'order' => $exercise->pivot->order,
                    'notes' => $exercise->pivot->notes,
                ],
                'completed' => $this->relationLoaded('completions') && $this->sessionId
                    ? $this->completions
                        ->where('workout_session_id', $this->sessionId)
                        ->where('user_id', auth()->id())
                        ->contains('exercise_id', $exercise->id)
                    : false,
            ])),
            'is_active' => $this->is_active,
            'total_sessions' => (int) ($this->total_sessions ?? 0),
            'last_completed_at' => $this->last_completed_at,
            'avg_duration_minutes' => $this->avg_duration_minutes ? (int) round((float) $this->avg_duration_minutes) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
