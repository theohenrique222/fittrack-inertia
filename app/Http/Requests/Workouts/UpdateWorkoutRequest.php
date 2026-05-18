<?php

namespace App\Http\Requests\Workouts;

use App\Models\Client;
use App\Models\Exercise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workoutId = $this->route('workout')->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('workouts')->ignore($workoutId)],
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
