<?php

namespace App\Actions\Dashboard;

use App\Enums\UserRole;
use App\Models\BodyMeasurement;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\Trainer;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\DB;

class GetAdminDashboardStatsAction
{
    public function execute(User $user): array
    {
        $totalUsers = User::count();
        $totalClients = Client::count();
        $totalTrainers = User::where('role', UserRole::PERSONAL)->count();
        $totalExercises = DB::table('exercises')->where('is_active', true)->count();

        $usersByRole = $this->getUsersByRole();
        $monthlyGrowth = $this->getMonthlyGrowth();
        $systemActivity = $this->getSystemActivity();
        $recentUsers = $this->getRecentUsers();
        $topTrainers = $this->getTopTrainers();
        $platformMetrics = $this->getPlatformMetrics();

        return [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalStudents' => $totalClients,
                'totalTrainers' => $totalTrainers,
                'totalExercises' => $totalExercises,
            ],
            'usersByRole' => $usersByRole,
            'monthlyGrowth' => $monthlyGrowth,
            'systemActivity' => $systemActivity,
            'recentUsers' => $recentUsers,
            'topTrainers' => $topTrainers,
            'platformMetrics' => $platformMetrics,
            'quickActions' => [
                ['label' => 'Gerenciar Usuários', 'route' => '/users', 'icon' => 'users'],
                ['label' => 'Gerenciar Treinadores', 'route' => '/trainers', 'icon' => 'user-check'],
                ['label' => 'Todos os Alunos', 'route' => '/admin/students', 'icon' => 'dumbbell'],
                ['label' => 'Gerenciar Exercícios', 'route' => '/exercises', 'icon' => 'dumbbell'],
            ],
        ];
    }

    private function getUsersByRole(): array
    {
        $roles = [
            ['role' => 'Admin', 'key' => UserRole::ADMIN, 'color' => '#10b981'],
            ['role' => 'Personal Trainer', 'key' => UserRole::PERSONAL, 'color' => '#14b8a6'],
            ['role' => 'Cliente', 'key' => UserRole::CLIENT, 'color' => '#059669'],
            ['role' => 'Freelancer', 'key' => UserRole::SELF, 'color' => '#0d9488'],
        ];

        return collect($roles)->map(fn ($r) => [
            'role' => $r['role'],
            'count' => User::where('role', $r['key'])->count(),
            'color' => $r['color'],
        ])->all();
    }

    private function getMonthlyGrowth(): array
    {
        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        $usersPerMonth = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $trainersPerMonth = User::where('role', UserRole::PERSONAL)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $cumulativeUsers = 0;
        $cumulativeTrainers = 0;

        return $usersPerMonth->map(function ($count, $monthKey) use ($trainersPerMonth, $months, &$cumulativeUsers, &$cumulativeTrainers) {
            $cumulativeUsers += $count;
            $cumulativeTrainers += $trainersPerMonth->get($monthKey, 0);

            $monthNum = (int) substr($monthKey, 5, 2);

            return [
                'month' => $months[$monthNum - 1],
                'users' => $cumulativeUsers,
                'trainers' => $cumulativeTrainers,
            ];
        })->values()->all();
    }

    private function getSystemActivity(): array
    {
        $dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        $today = now();

        return collect(range(6, 0))->map(function ($daysAgo) use ($today, $dayNames) {
            $date = $today->copy()->subDays($daysAgo);

            $logins = DB::table('sessions')
                ->whereRaw('FROM_UNIXTIME(last_activity) >= ?', [$date->startOfDay()])
                ->whereRaw('FROM_UNIXTIME(last_activity) < ?', [$date->copy()->endOfDay()])
                ->count();

            $registrations = User::whereDate('created_at', $date)->count();

            return [
                'day' => $dayNames[$date->dayOfWeek],
                'logins' => $logins,
                'registrations' => $registrations,
            ];
        })->all();
    }

    private function getRecentUsers(): array
    {
        return User::latest()
            ->take(5)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->label() ?? 'N/A',
                'created_at' => $user->created_at?->format('d/m/Y H:i'),
            ])
            ->all();
    }

    private function getTopTrainers(): array
    {
        $studentCounts = User::whereNotNull('trainer_id')
            ->selectRaw('trainer_id, COUNT(*) as count')
            ->groupBy('trainer_id')
            ->pluck('count', 'trainer_id');

        return Trainer::with('user')
            ->get()
            ->map(function ($trainer) use ($studentCounts) {
                return [
                    'name' => $trainer->user->name,
                    'students' => $studentCounts->get($trainer->user_id, 0),
                    'specialty' => $trainer->specialty ?? 'Geral',
                    'rating' => 4.5,
                ];
            })
            ->sortByDesc('students')
            ->values()
            ->all();
    }

    private function getPlatformMetrics(): array
    {
        $totalWorkouts = Workout::count();
        $totalSessions = WorkoutSession::count();
        $totalMeasurements = BodyMeasurement::count();
        $totalExercises = Exercise::where('is_active', true)->count();

        return [
            ['label' => 'Total de Treinos', 'value' => (string) $totalWorkouts, 'change' => 'Planos de treino', 'trend' => 'up'],
            ['label' => 'Sessões Realizadas', 'value' => (string) $totalSessions, 'change' => 'Treinos executados', 'trend' => 'up'],
            ['label' => 'Medidas Registradas', 'value' => (string) $totalMeasurements, 'change' => 'Avaliações físicas', 'trend' => 'up'],
            ['label' => 'Exercícios', 'value' => (string) $totalExercises, 'change' => 'No catálogo', 'trend' => 'up'],
        ];
    }
}
