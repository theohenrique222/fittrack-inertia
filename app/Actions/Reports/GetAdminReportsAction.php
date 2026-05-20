<?php

namespace App\Actions\Reports;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetAdminReportsAction
{
    public function execute(string $period, ?CarbonInterface $startDate, ?CarbonInterface $endDate): array
    {
        [$start, $end] = $this->resolveDateRange($period, $startDate, $endDate);
        $previousStart = $start->clone()->subDays($start->diffInDays($end));

        return [
            'overview' => $this->getOverview($start, $end, $previousStart),
            'usersByRole' => $this->getUsersByRole(),
            'registrationsOverTime' => $this->getRegistrationsOverTime($start, $end),
            'topTrainers' => $this->getTopTrainers($start, $end),
            'mostUsedExercises' => $this->getMostUsedExercises($start, $end),
            'exercisesByMuscleGroup' => $this->getExercisesByMuscleGroup(),
            'workoutsByTrainer' => $this->getWorkoutsByTrainer($start, $end),
            'usersTable' => $this->getUsersTable($start, $end),
        ];
    }

    private function resolveDateRange(string $period, ?CarbonInterface $startDate, ?CarbonInterface $endDate): array
    {
        if ($period === 'custom' && $startDate && $endDate) {
            return [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()];
        }

        $end = now();
        $start = match ($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '12m' => now()->subMonths(12),
            default => now()->subDays(30),
        };

        return [$start, $end];
    }

    private function getOverview(CarbonInterface $start, CarbonInterface $end, CarbonInterface $previousStart): array
    {
        $currentUsers = User::whereBetween('created_at', [$start, $end])->count();
        $previousUsers = User::whereBetween('created_at', [$previousStart, $start])->count();
        $currentClients = Client::whereBetween('created_at', [$start, $end])->count();
        $previousClients = Client::whereBetween('created_at', [$previousStart, $start])->count();
        $currentTrainers = User::where('role', UserRole::PERSONAL)->whereBetween('created_at', [$start, $end])->count();
        $previousTrainers = User::where('role', UserRole::PERSONAL)->whereBetween('created_at', [$previousStart, $start])->count();
        $currentWorkouts = DB::table('workouts')->whereBetween('created_at', [$start, $end])->count();
        $previousWorkouts = DB::table('workouts')->whereBetween('created_at', [$previousStart, $start])->count();

        return [
            'totalUsers' => User::count(),
            'totalClients' => Client::count(),
            'totalTrainers' => User::where('role', UserRole::PERSONAL)->count(),
            'totalExercises' => DB::table('exercises')->where('is_active', true)->count(),
            'totalWorkouts' => DB::table('workouts')->count(),
            'newUsers' => $currentUsers,
            'newUsersChange' => $this->calculateChange($currentUsers, $previousUsers),
            'newClients' => $currentClients,
            'newClientsChange' => $this->calculateChange($currentClients, $previousClients),
            'newTrainers' => $currentTrainers,
            'newTrainersChange' => $this->calculateChange($currentTrainers, $previousTrainers),
            'newWorkouts' => $currentWorkouts,
            'newWorkoutsChange' => $this->calculateChange($currentWorkouts, $previousWorkouts),
        ];
    }

    private function calculateChange(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getUsersByRole(): array
    {
        $roleLabels = [
            'admin' => 'Administrador',
            'personal' => 'Personal Trainer',
            'client' => 'Cliente',
            'self' => 'Freelancer',
        ];

        $colors = [
            'admin' => '#10b981',
            'personal' => '#14b8a6',
            'client' => '#059669',
            'self' => '#0d9488',
        ];

        return User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get()
            ->map(fn ($row) => [
                'role' => $roleLabels[$row->role->value] ?? ucfirst($row->role->value),
                'count' => (int) $row->count,
                'color' => $colors[$row->role->value] ?? '#6b7280',
            ])
            ->all();
    }

    private function getRegistrationsOverTime(CarbonInterface $start, CarbonInterface $end): array
    {
        $days = $start->diffInDays($end);

        if ($days <= 31) {
            return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date' => Carbon::parse($row->date)->format('d/m'),
                    'count' => (int) $row->count,
                ])
                ->all();
        }

        if ($days <= 95) {
            return User::selectRaw('YEARWEEK(created_at) as week, MIN(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date' => Carbon::parse($row->date)->format('d/m'),
                    'count' => (int) $row->count,
                ])
                ->all();
        }

        return User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::createFromFormat('Y-m', $row->month)->format('M/Y'),
                'count' => (int) $row->count,
            ])
            ->all();
    }

    private function getTopTrainers(CarbonInterface $start, CarbonInterface $end): array
    {
        return User::where('role', UserRole::PERSONAL)
            ->withCount(['students as students_count'])
            ->orderByDesc('students_count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'specialty' => 'Não informada',
                'studentsCount' => (int) $row->students_count,
            ])
            ->all();
    }

    private function getMostUsedExercises(CarbonInterface $start, CarbonInterface $end): array
    {
        return DB::table('exercise_workout')
            ->join('exercises', 'exercise_workout.exercise_id', '=', 'exercises.id')
            ->join('workouts', 'exercise_workout.workout_id', '=', 'workouts.id')
            ->whereBetween('workouts.created_at', [$start, $end])
            ->selectRaw('exercises.name, exercises.muscle_group, COUNT(*) as usage_count')
            ->groupBy('exercises.id', 'exercises.name', 'exercises.muscle_group')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'muscleGroup' => $row->muscle_group ?? 'N/A',
                'count' => (int) $row->usage_count,
            ])
            ->all();
    }

    private function getExercisesByMuscleGroup(): array
    {
        $colors = ['#10b981', '#14b8a6', '#059669', '#0d9488', '#047857', '#065f46', '#064e3b', '#34d399'];

        return DB::table('exercises')
            ->where('is_active', true)
            ->selectRaw('muscle_group, COUNT(*) as count')
            ->groupBy('muscle_group')
            ->orderByDesc('count')
            ->get()
            ->map(function ($row, $index) use ($colors) {
                return [
                    'name' => $row->muscle_group ?? 'Não definido',
                    'value' => (int) $row->count,
                    'color' => $colors[$index % count($colors)],
                ];
            })
            ->all();
    }

    private function getWorkoutsByTrainer(CarbonInterface $start, CarbonInterface $end): array
    {
        return DB::table('workouts')
            ->join('users', 'workouts.trainer_id', '=', 'users.id')
            ->whereBetween('workouts.created_at', [$start, $end])
            ->selectRaw('users.name, COUNT(workouts.id) as workout_count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('workout_count')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'trainer' => $row->name,
                'count' => (int) $row->workout_count,
            ])
            ->all();
    }

    private function getUsersTable(CarbonInterface $start, CarbonInterface $end): array
    {
        return User::with('trainer')
            ->latest()
            ->take(50)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->label() ?? 'N/A',
                'createdAt' => $user->created_at?->format('d/m/Y H:i'),
            ])
            ->all();
    }
}
