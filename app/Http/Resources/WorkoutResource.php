<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
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
                    'order' => $exercise->pivot->order,
                    'notes' => $exercise->pivot->notes,
                ],
                'completed' => $this->relationLoaded('completions')
                    ? $this->completions->contains('exercise_id', $exercise->id)
                    : false,
            ])),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
