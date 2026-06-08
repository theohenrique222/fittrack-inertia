<?php

namespace App\Http\Requests\Plans;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['nullable', 'integer', Rule::exists(Plan::class, 'id')->where('is_active', true)],
        ];
    }
}
