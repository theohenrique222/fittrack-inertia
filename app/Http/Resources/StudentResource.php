<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user?->name ?? 'Sem nome',
            'email' => $this->user?->email ?? '',
            'nickname' => $this->nickname,
            'trainer_name' => $this->user?->trainer?->name ?? null,
            'plan' => $this->whenLoaded('plan', fn () => $this->plan ? [
                'id' => $this->plan->id,
                'name' => $this->plan->name,
                'price' => (float) $this->plan->price,
            ] : null),
            'plan_id' => $this->plan_id,
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
