<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'plan_id' => $this->plan_id,
            'amount' => (float) $this->amount,
            'due_date' => $this->due_date,
            'paid_at' => $this->paid_at,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'notes' => $this->notes,
            'client' => $this->whenLoaded('client', fn () => [
                'id' => $this->client->id,
                'nickname' => $this->client->nickname,
                'user' => $this->client->relationLoaded('user') ? [
                    'id' => $this->client->user->id,
                    'name' => $this->client->user->name,
                ] : null,
            ]),
            'plan' => $this->whenLoaded('plan', fn () => [
                'id' => $this->plan->id,
                'name' => $this->plan->name,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
