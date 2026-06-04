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
            'weight' => ['required', 'numeric', 'min:20', 'max:700'],
            'height' => ['required', 'numeric', 'min:50', 'max:300'],
            'neck' => ['nullable', 'numeric', 'min:5', 'max:120'],
            'waist' => ['nullable', 'numeric', 'min:20', 'max:350'],
            'hip' => ['nullable', 'numeric', 'min:20', 'max:350'],
            'chest' => ['nullable', 'numeric', 'min:20', 'max:350'],
            'thigh' => ['nullable', 'numeric', 'min:5', 'max:200'],
            'arm' => ['nullable', 'numeric', 'min:5', 'max:150'],
            'forearm' => ['nullable', 'numeric', 'min:5', 'max:100'],
            'calf' => ['nullable', 'numeric', 'min:5', 'max:120'],
            'shoulders' => ['nullable', 'numeric', 'min:20', 'max:350'],
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

    public function getCautionWarnings(): array
    {
        $warnings = [];

        $cautionThresholds = [
            'weight' => ['min' => 25, 'max' => 300, 'label' => 'Peso'],
            'height' => ['min' => 80, 'max' => 230, 'label' => 'Altura'],
            'neck' => ['max' => 70, 'label' => 'Pescoço'],
            'waist' => ['max' => 200, 'label' => 'Cintura'],
            'hip' => ['max' => 200, 'label' => 'Quadril'],
            'chest' => ['max' => 200, 'label' => 'Peitoral'],
            'thigh' => ['max' => 100, 'label' => 'Coxa'],
            'arm' => ['max' => 70, 'label' => 'Braço'],
            'forearm' => ['max' => 50, 'label' => 'Antebraço'],
            'calf' => ['max' => 60, 'label' => 'Panturrilha'],
            'shoulders' => ['max' => 200, 'label' => 'Ombros'],
        ];

        foreach ($cautionThresholds as $field => $thresholds) {
            $value = $this->input($field);

            if ($value === null || $value === '') {
                continue;
            }

            if (isset($thresholds['min']) && (float) $value < $thresholds['min']) {
                $warnings[] = "{$thresholds['label']} ({$value}) está muito baixo. Verifique se o valor está correto.";
            }

            if (isset($thresholds['max']) && (float) $value > $thresholds['max']) {
                $warnings[] = "{$thresholds['label']} ({$value}) está muito alto. Verifique se o valor está correto.";
            }
        }

        return $warnings;
    }

    public function messages(): array
    {
        return [
            'weight.required' => 'O peso é obrigatório.',
            'weight.min' => 'O peso deve ser no mínimo 20 kg.',
            'weight.max' => 'O peso deve ser no máximo 700 kg.',
            'height.required' => 'A altura é obrigatória.',
            'height.min' => 'A altura deve ser no mínimo 50 cm.',
            'height.max' => 'A altura deve ser no máximo 300 cm.',
            'activity_level.required' => 'O nível de atividade é obrigatório.',
            'activity_level.in' => 'Nível de atividade inválido.',
            'goal.required' => 'O objetivo é obrigatório.',
            'goal.in' => 'Objetivo inválido.',
        ];
    }
}
