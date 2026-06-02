<?php

namespace App\Actions\BodyMeasurements;

use App\Enums\ActivityLevel;
use App\Enums\Goal;
use App\Models\BodyMeasurement;
use App\Models\User;

class BodyMetricsCalculator
{
    private float $weight;

    private float $height;

    private string $gender;

    private int $age;

    private ?float $neck;

    private ?float $waist;

    private ?float $hip;

    private ActivityLevel $activityLevel;

    private Goal $goal;

    public function __construct(
        private BodyMeasurement $measurement,
        private User $user,
    ) {
        if (! $user->gender || ! $user->birthdate) {
            throw new \RuntimeException('Usuário precisa ter gênero e data de nascimento para calcular métricas.');
        }

        if (! $user->age()) {
            throw new \RuntimeException('Não foi possível calcular a idade do usuário.');
        }

        $this->weight = (float) $measurement->weight;
        $this->height = (float) $measurement->height;
        $this->gender = $user->gender->value;
        $this->age = $user->age();
        $this->neck = $measurement->neck ? (float) $measurement->neck : null;
        $this->waist = $measurement->waist ? (float) $measurement->waist : null;
        $this->hip = $measurement->hip ? (float) $measurement->hip : null;
        $this->activityLevel = $measurement->activity_level;
        $this->goal = $measurement->goal;
    }

    public function calculate(): array
    {
        $bmi = $this->calculateBmi();
        $bodyFat = $this->calculateBodyFat();
        $leanMass = $this->calculateLeanMass($bodyFat);
        $bmr = $this->calculateBmr();
        $tdee = $this->calculateTdee($bmr);
        $dailyWater = $this->calculateDailyWater();
        $macros = $this->calculateMacros($tdee);

        return [
            'bmi' => [
                'value' => round($bmi, 1),
                'classification' => $this->classifyBmi($bmi),
                'color' => $this->bmiColor($bmi),
            ],
            'body_fat' => [
                'value' => round($bodyFat, 1),
                'classification' => $this->classifyBodyFat($bodyFat),
                'color' => $this->bodyFatColor($bodyFat),
            ],
            'lean_mass' => round($leanMass, 1),
            'fat_mass' => round($this->weight - $leanMass, 1),
            'bmr' => round($bmr),
            'tdee' => round($tdee),
            'daily_water' => round($dailyWater),
            'macros' => $macros,
        ];
    }

    private function calculateBmi(): float
    {
        $heightInMeters = $this->height / 100;

        return $this->weight / ($heightInMeters * $heightInMeters);
    }

    private function calculateBodyFat(): float
    {
        if (! $this->neck || ! $this->waist) {
            return 0;
        }

        $height = $this->height;

        if ($this->gender === 'male') {
            return 86.010 * log10($this->waist - $this->neck)
                - 70.041 * log10($height)
                + 36.76;
        }

        if (! $this->hip) {
            return 0;
        }

        return 163.205 * log10($this->waist + $this->hip - $this->neck)
            - 97.684 * log10($height)
            - 78.387;
    }

    private function calculateLeanMass(float $bodyFat): float
    {
        if ($bodyFat <= 0) {
            return 0;
        }

        return $this->weight * (1 - $bodyFat / 100);
    }

    private function calculateBmr(): float
    {
        if ($this->gender === 'male') {
            return 10 * $this->weight + 6.25 * $this->height - 5 * $this->age + 5;
        }

        return 10 * $this->weight + 6.25 * $this->height - 5 * $this->age - 161;
    }

    private function calculateTdee(float $bmr): float
    {
        $tdee = $bmr * $this->activityLevel->factor();

        return match ($this->goal) {
            Goal::Lose => $tdee - 500,
            Goal::Gain => $tdee + 300,
            default => $tdee,
        };
    }

    private function calculateDailyWater(): float
    {
        return $this->weight * 35;
    }

    private function calculateMacros(float $tdee): array
    {
        $proteinGrams = 2 * $this->weight;
        $proteinCalories = $proteinGrams * 4;

        $fatCalories = $tdee * 0.25;
        $fatGrams = $fatCalories / 9;

        $carbsCalories = $tdee - $proteinCalories - $fatCalories;
        $carbsGrams = max(0, $carbsCalories / 4);

        return [
            'calories' => round($tdee),
            'protein' => [
                'grams' => round($proteinGrams),
                'percentage' => round(($proteinCalories / $tdee) * 100),
            ],
            'carbs' => [
                'grams' => round($carbsGrams),
                'percentage' => round(($carbsCalories / $tdee) * 100),
            ],
            'fat' => [
                'grams' => round($fatGrams),
                'percentage' => round(($fatCalories / $tdee) * 100),
            ],
        ];
    }

    private function classifyBmi(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => 'Abaixo do peso',
            $bmi < 25 => 'Peso normal',
            $bmi < 30 => 'Sobrepeso',
            $bmi < 35 => 'Obesidade grau I',
            $bmi < 40 => 'Obesidade grau II',
            default => 'Obesidade grau III',
        };
    }

    private function bmiColor(float $bmi): string
    {
        return match (true) {
            $bmi < 18.5 => '#f59e0b',
            $bmi < 25 => '#10b981',
            $bmi < 30 => '#f59e0b',
            $bmi < 35 => '#f97316',
            $bmi < 40 => '#ef4444',
            default => '#dc2626',
        };
    }

    private function classifyBodyFat(float $bodyFat): string
    {
        if ($bodyFat <= 0) {
            return 'Não calculado';
        }

        if ($this->gender === 'male') {
            return match (true) {
                $bodyFat < 6 => 'Essencial',
                $bodyFat < 14 => 'Atlético',
                $bodyFat < 18 => 'Boa forma',
                $bodyFat < 25 => 'Aceitável',
                default => 'Obeso',
            };
        }

        return match (true) {
            $bodyFat < 14 => 'Essencial',
            $bodyFat < 21 => 'Atlético',
            $bodyFat < 25 => 'Boa forma',
            $bodyFat < 32 => 'Aceitável',
            default => 'Obeso',
        };
    }

    private function bodyFatColor(float $bodyFat): string
    {
        if ($bodyFat <= 0) {
            return '#6b7280';
        }

        if ($this->gender === 'male') {
            return match (true) {
                $bodyFat < 6 => '#f59e0b',
                $bodyFat < 14 => '#10b981',
                $bodyFat < 18 => '#10b981',
                $bodyFat < 25 => '#f59e0b',
                default => '#ef4444',
            };
        }

        return match (true) {
            $bodyFat < 14 => '#f59e0b',
            $bodyFat < 21 => '#10b981',
            $bodyFat < 25 => '#10b981',
            $bodyFat < 32 => '#f59e0b',
            default => '#ef4444',
        };
    }
}
