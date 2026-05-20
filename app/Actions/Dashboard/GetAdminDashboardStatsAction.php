<?php

namespace App\Actions\Dashboard;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetAdminDashboardStatsAction
{
    public function execute(User $user): array
    {
        $totalUsers = User::count();
        $totalClients = Client::count();
        $totalTrainers = User::where('role', UserRole::PERSONAL)->count();
        $totalExercises = DB::table('exercises')->where('is_active', true)->count();
        $totalCategories = DB::table('categories')->where('is_active', true)->count();

        $usersByRole = $this->getUsersByRole();
        $monthlyGrowth = $this->generateMonthlyGrowth();
        $systemActivity = $this->generateSystemActivity();
        $recentUsers = $this->getRecentUsers();
        $topTrainers = $this->generateTopTrainers();
        $platformMetrics = $this->generatePlatformMetrics();

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

    private function generateMonthlyGrowth(): array
    {
        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $users = [15, 22, 28, 35, 42, 48, 55, 62, 68, 75, 82, 88];
        $trainers = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 11, 12];

        return collect($months)->map(fn ($month, $i) => [
            'month' => $month,
            'users' => $users[$i],
            'trainers' => $trainers[$i],
        ])->all();
    }

    private function generateSystemActivity(): array
    {
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        $logins = [45, 62, 38, 55, 72, 28, 15];
        $registrations = [3, 5, 2, 4, 6, 1, 0];

        return collect($days)->map(fn ($day, $i) => [
            'day' => $day,
            'logins' => $logins[$i],
            'registrations' => $registrations[$i],
        ])->all();
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

    private function generateTopTrainers(): array
    {
        return [
            ['name' => 'Carlos Silva', 'students' => 15, 'specialty' => 'Musculação', 'rating' => 4.9],
            ['name' => 'Ana Santos', 'students' => 12, 'specialty' => 'Funcional', 'rating' => 4.8],
            ['name' => 'Pedro Oliveira', 'students' => 10, 'specialty' => 'Crossfit', 'rating' => 4.7],
            ['name' => 'Maria Costa', 'students' => 8, 'specialty' => 'Yoga', 'rating' => 4.9],
        ];
    }

    private function generatePlatformMetrics(): array
    {
        return [
            ['label' => 'Uptime', 'value' => '99.9%', 'change' => '+0.1%', 'trend' => 'up'],
            ['label' => 'Tempo de Resposta', 'value' => '245ms', 'change' => '-15ms', 'trend' => 'down'],
            ['label' => 'Armazenamento', 'value' => '68%', 'change' => '+5%', 'trend' => 'up'],
            ['label' => 'API Requests/dia', 'value' => '12.5k', 'change' => '+8%', 'trend' => 'up'],
        ];
    }
}
