<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'duration_months' => $this->duration_months,
            'is_active' => $this->is_active,
            'clients_count' => $this->whenCounted('clients'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
