<?php

namespace App\Http\Requests\BodyMeasurements;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class StoreBodyMeasurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user->isAdmin()) {
            return true;
        }

        $client = $this->route('student');

        if ($user->isPersonal() || $user->isSelf()) {
            return $client instanceof Client
                && $client->user->trainer_id === $user->id;
        }

        if ($user->isStudent()) {
            return $client instanceof Client
                && $client->user_id === $user->id;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'recorded_at' => ['nullable', 'date'],
            'weight' => ['required', 'numeric', 'min:20', 'max:350'],
            'height' => ['required', 'numeric', 'min:100', 'max:250'],
            'neck' => ['nullable', 'numeric', 'min:20', 'max:70'],
            'waist' => ['nullable', 'numeric', 'min:40', 'max:200'],
            'hip' => ['nullable', 'numeric', 'min:50', 'max:200'],
            'chest' => ['nullable', 'numeric', 'min:50', 'max:200'],
            'thigh' => ['nullable', 'numeric', 'min:20', 'max:100'],
            'arm' => ['nullable', 'numeric', 'min:15', 'max:70'],
            'forearm' => ['nullable', 'numeric', 'min:15', 'max:50'],
            'calf' => ['nullable', 'numeric', 'min:15', 'max:60'],
            'shoulders' => ['nullable', 'numeric', 'min:60', 'max:200'],
            'activity_level' => ['required', 'string', 'in:sedentary,light,moderate,active,very_active'],
            'goal' => ['required', 'string', 'in:maintain,lose,gain'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();

            if (! $user->gender || ! $user->birthdate) {
                $validator->errors()->add(
                    'profile_incomplete',
                    'Complete seu perfil com gênero e data de nascimento antes de registrar medidas.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'weight.required' => 'O peso é obrigatório.',
            'weight.min' => 'O peso deve ser no mínimo 20 kg.',
            'weight.max' => 'O peso deve ser no máximo 350 kg.',
            'height.required' => 'A altura é obrigatória.',
            'height.min' => 'A altura deve ser no mínimo 100 cm.',
            'height.max' => 'A altura deve ser no máximo 250 cm.',
            'activity_level.required' => 'O nível de atividade é obrigatório.',
            'activity_level.in' => 'Nível de atividade inválido.',
            'goal.required' => 'O objetivo é obrigatório.',
            'goal.in' => 'Objetivo inválido.',
        ];
    }
}
