<?php

namespace App\Http\Requests\Workouts;

use App\Models\Category;
use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255', 'unique:workouts'],
            'description' => ['nullable', 'string'],
            'client_id' => ['required', 'integer', Rule::in(Client::pluck('id')->toArray())],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['required', 'integer', Rule::in(Category::pluck('id')->toArray())],
        ];
    }
}
