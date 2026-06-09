<?php

namespace App\Http\Requests\Financial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FinancialFilterRequest extends FormRequest
{
    /**
     * @return array<string, list<ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'period' => ['sometimes', 'in:7d,30d,90d,12m,custom'],
            'start_date' => ['required_if:period,custom', 'nullable', 'date'],
            'end_date' => ['required_if:period,custom', 'nullable', 'date', 'after_or_equal:start_date'],
            'student_id' => ['sometimes', 'nullable', 'exists:clients,id'],
            'status' => ['sometimes', 'nullable', 'in:pending,paid,overdue'],
            'payment_method' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
