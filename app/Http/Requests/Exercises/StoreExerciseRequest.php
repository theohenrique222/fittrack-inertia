<?php

namespace App\Http\Requests\Exercises;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:exercises'],
            'description' => ['nullable', 'string'],
            'muscle_group' => ['required', 'string', 'in:Chest,Back,Shoulders,Biceps,Triceps,Forearms,Abs,Quadriceps,Hamstrings,Glutes,Calves,Full Body'],
            'equipment' => ['nullable', 'string', 'in:Dumbbell,Barbell,Machine,Cable,Bodyweight,Resistance Band,Kettlebell,Smith Machine'],
            'difficulty' => ['required', 'string', 'in:Beginner,Intermediate,Advanced'],
            'instructions' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'url'],
            'image' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
