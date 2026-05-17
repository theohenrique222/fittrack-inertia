<?php

namespace App\Http\Requests\Exercises;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $exerciseId = $this->route('exercise')->id;

        return [
            'name' => ['required', 'string', 'max:255', "unique:exercises,name,{$exerciseId}"],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'integer', Rule::in(Category::pluck('id')->toArray())],
            'equipment' => ['nullable', 'string', 'in:Dumbbell,Barbell,Machine,Cable,Bodyweight,Resistance Band,Kettlebell,Smith Machine'],
            'difficulty' => ['required', 'string', 'in:Beginner,Intermediate,Advanced'],
            'instructions' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'url'],
            'image' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
