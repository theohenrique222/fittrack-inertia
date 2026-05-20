<?php

namespace App\Actions\Dashboard;

use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetTrainerDashboardStatsAction
{
    public function execute(User $user): array
    {
        $totalClients = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $user->id))->count();
        $activeClients = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $user->id))->count();
        $totalExercises = DB::table('exercises')->where('is_active', true)->count();
        $totalCategories = DB::table('categories')->where('is_active', true)->count();

        $recentClients = Client::with('user')
            ->whereHas('user', fn ($q) => $q->where('trainer_id', $user->id))
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($student) => [
                'id' => $student->id,
                'name' => $student->user?->name ?? 'Sem nome',
                'email' => $student->user?->email ?? '',
                'nickname' => $student->nickname,
                'created_at' => $student->created_at?->format('d/m/Y'),
            ]);

        $weeklyActivity = $this->generateWeeklyActivity();
        $muscleGroupDistribution = $this->generateMuscleGroupDistribution();
        $monthlyGrowth = $this->generateMonthlyGrowth();

        return [
            'stats' => [
                'totalStudents' => $totalClients,
                'activeStudents' => $activeClients,
                'totalExercises' => $totalExercises,
                'totalCategories' => $totalCategories,
            ],
            'recentStudents' => $recentClients,
            'weeklyActivity' => $weeklyActivity,
            'muscleGroupDistribution' => $muscleGroupDistribution,
            'monthlyGrowth' => $monthlyGrowth,
            'upcomingWorkouts' => $this->generateUpcomingWorkouts(),
            'quickActions' => [
                ['label' => 'Novo Cliente', 'route' => '/clients', 'icon' => 'user-plus'],
                ['label' => 'Novo Exercício', 'route' => '/exercises', 'icon' => 'dumbbell'],
                ['label' => 'Novo Treino', 'route' => '/workouts', 'icon' => 'clipboard-list'],
                ['label' => 'Relatórios', 'route' => '/reports', 'icon' => 'bar-chart-3'],
            ],
        ];
    }

    private function generateWeeklyActivity(): array
    {
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        $values = [12, 19, 8, 15, 22, 14, 6];

        return collect($days)->map(fn ($day, $i) => [
            'day' => $day,
            'value' => $values[$i],
        ])->all();
    }

    private function generateMuscleGroupDistribution(): array
    {
        return [
            ['name' => 'Peito', 'value' => 18, 'color' => '#10b981'],
            ['name' => 'Costas', 'value' => 15, 'color' => '#14b8a6'],
            ['name' => 'Pernas', 'value' => 22, 'color' => '#059669'],
            ['name' => 'Ombros', 'value' => 12, 'color' => '#0d9488'],
            ['name' => 'Braços', 'value' => 14, 'color' => '#047857'],
            ['name' => 'Core', 'value' => 9, 'color' => '#065f46'],
        ];
    }

    private function generateMonthlyGrowth(): array
    {
        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $students = [4, 6, 8, 12, 15, 18, 22, 28, 32, 38, 42, 48];
        $workouts = [20, 35, 45, 60, 75, 90, 110, 135, 155, 180, 200, 230];

        return collect($months)->map(fn ($month, $i) => [
            'month' => $month,
            'students' => $students[$i],
            'workouts' => $workouts[$i],
        ])->all();
    }

    private function generateUpcomingWorkouts(): array
    {
        return [
            [
                'student' => 'João Silva',
                'type' => 'Treino A - Peito & Tríceps',
                'time' => '08:00',
                'status' => 'scheduled',
            ],
            [
                'student' => 'Maria Santos',
                'type' => 'Treino B - Costas & Bíceps',
                'time' => '10:00',
                'status' => 'scheduled',
            ],
            [
                'student' => 'Pedro Oliveira',
                'type' => 'Treino C - Pernas',
                'time' => '14:00',
                'status' => 'scheduled',
            ],
            [
                'student' => 'Ana Costa',
                'type' => 'Treino D - Ombros & Core',
                'time' => '16:00',
                'status' => 'scheduled',
            ],
        ];
    }
}
