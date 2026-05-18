<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetClientDashboardStatsAction
{
    public function execute(User $user): array
    {
        $totalWorkouts = 24;
        $completedWorkouts = 18;
        $currentStreak = 7;
        $totalExercises = DB::table('exercises')->where('is_active', true)->count();

        $weeklyWorkouts = $this->generateWeeklyWorkouts();
        $progressData = $this->generateProgressData();
        $nutritionData = $this->generateNutritionData();
        $bodyMetrics = $this->generateBodyMetrics();

        return [
            'stats' => [
                'totalWorkouts' => $totalWorkouts,
                'completedWorkouts' => $completedWorkouts,
                'currentStreak' => $currentStreak,
                'totalExercises' => $totalExercises,
            ],
            'weeklyWorkouts' => $weeklyWorkouts,
            'progressData' => $progressData,
            'nutritionData' => $nutritionData,
            'bodyMetrics' => $bodyMetrics,
            'upcomingWorkouts' => $this->generateUpcomingWorkouts(),
            'recentAchievements' => $this->generateRecentAchievements(),
            'trainer' => [
                'name' => 'Carlos Personal',
                'specialty' => 'Musculação & Funcional',
                'email' => 'carlos@fittrack.com',
            ],
        ];
    }

    private function generateWeeklyWorkouts(): array
    {
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        $completed = [true, true, false, true, true, false, false];
        $types = ['Peito', 'Costas', 'Descanso', 'Pernas', 'Ombros', 'Descanso', 'Descanso'];

        return collect($days)->map(fn ($day, $i) => [
            'day' => $day,
            'completed' => $completed[$i],
            'type' => $types[$i],
        ])->all();
    }

    private function generateProgressData(): array
    {
        $weeks = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8'];
        $weight = [70, 70.5, 71, 71.2, 71.8, 72, 72.5, 73];
        $bodyFat = [18, 17.5, 17, 16.8, 16.2, 15.8, 15.5, 15];

        return collect($weeks)->map(fn ($week, $i) => [
            'week' => $week,
            'weight' => $weight[$i],
            'bodyFat' => $bodyFat[$i],
        ])->all();
    }

    private function generateNutritionData(): array
    {
        return [
            'calories' => [
                'consumed' => 2150,
                'target' => 2400,
                'percentage' => 90,
            ],
            'protein' => [
                'consumed' => 145,
                'target' => 160,
                'percentage' => 91,
                'unit' => 'g',
            ],
            'carbs' => [
                'consumed' => 220,
                'target' => 280,
                'percentage' => 79,
                'unit' => 'g',
            ],
            'fat' => [
                'consumed' => 65,
                'target' => 75,
                'percentage' => 87,
                'unit' => 'g',
            ],
        ];
    }

    private function generateBodyMetrics(): array
    {
        return [
            ['label' => 'Peso', 'value' => '73 kg', 'change' => '+3 kg', 'trend' => 'up'],
            ['label' => 'Gordura Corporal', 'value' => '15%', 'change' => '-3%', 'trend' => 'down'],
            ['label' => 'Massa Magra', 'value' => '62 kg', 'change' => '+2 kg', 'trend' => 'up'],
            ['label' => 'IMC', 'value' => '24.2', 'change' => '+0.5', 'trend' => 'up'],
        ];
    }

    private function generateUpcomingWorkouts(): array
    {
        return [
            [
                'name' => 'Treino A - Peito & Tríceps',
                'date' => 'Hoje',
                'time' => '18:00',
                'exercises' => 6,
                'status' => 'scheduled',
            ],
            [
                'name' => 'Treino B - Costas & Bíceps',
                'date' => 'Amanhã',
                'time' => '18:00',
                'exercises' => 5,
                'status' => 'scheduled',
            ],
            [
                'name' => 'Treino C - Pernas',
                'date' => 'Quarta',
                'time' => '18:00',
                'exercises' => 7,
                'status' => 'scheduled',
            ],
        ];
    }

    private function generateRecentAchievements(): array
    {
        return [
            ['title' => '7 dias de sequência!', 'description' => 'Continue assim!', 'icon' => 'flame', 'date' => 'Hoje'],
            ['title' => 'Superação no supino', 'description' => 'Novo recorde: 80kg', 'icon' => 'trophy', 'date' => 'Ontem'],
            ['title' => '18 treinos completados', 'description' => '75% de conclusão', 'icon' => 'check-circle', 'date' => 'Esta semana'],
            ['title' => 'Meta de proteína batida', 'description' => '145g consumidos', 'icon' => 'target', 'date' => 'Esta semana'],
        ];
    }
}
