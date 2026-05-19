<?php

namespace App\Http\Requests\Workouts;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Exercise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user?->role === UserRole::ADMIN) {
            return true;
        }

        $clientId = $this->input('client_id');
        if (! $clientId) {
            return false;
        }

        return Client::where('id', $clientId)
            ->whereHas('user', fn ($q) => $q->where('trainer_id', $user?->id))
            ->exists();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:workouts'],
            'description' => ['nullable', 'string'],
            'client_id' => ['required', 'integer', Rule::in(Client::pluck('id')->toArray())],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.exercise_id' => ['required', 'integer', Rule::in(Exercise::pluck('id')->toArray())],
            'exercises.*.sets' => ['required', 'integer', 'min:1'],
            'exercises.*.reps' => ['required', 'integer', 'min:1'],
            'exercises.*.rest_seconds' => ['required', 'integer', 'min:0'],
            'exercises.*.order' => ['required', 'integer', 'min:0'],
            'exercises.*.notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
