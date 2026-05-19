<?php

namespace App\Actions\Reports;

use App\Models\Client;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetTrainerReportsAction
{
    public function execute(User $user, string $period, ?CarbonInterface $startDate, ?CarbonInterface $endDate): array
    {
        [$start, $end] = $this->resolveDateRange($period, $startDate, $endDate);
        $previousStart = $start->clone()->subDays($start->diffInDays($end));
        $trainerId = $user->id;

        return [
            'overview' => $this->getOverview($trainerId, $start, $end, $previousStart),
            'studentsByStatus' => $this->getStudentsByStatus($trainerId),
            'workoutsOverTime' => $this->getWorkoutsOverTime($trainerId, $start, $end),
            'mostUsedExercises' => $this->getMostUsedExercises($trainerId, $start, $end),
            'exercisesByMuscleGroup' => $this->getExercisesByMuscleGroup($trainerId),
            'workoutVolume' => $this->getWorkoutVolume($trainerId, $start, $end),
            'studentsTable' => $this->getStudentsTable($trainerId),
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

    private function getOverview(int $trainerId, CarbonInterface $start, CarbonInterface $end, CarbonInterface $previousStart): array
    {
        $currentStudents = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $previousStudents = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))
            ->whereBetween('created_at', [$previousStart, $start])
            ->count();

        $currentWorkouts = DB::table('workouts')
            ->where('trainer_id', $trainerId)
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $previousWorkouts = DB::table('workouts')
            ->where('trainer_id', $trainerId)
            ->whereBetween('created_at', [$previousStart, $start])
            ->count();

        $activeStudents = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))
            ->whereHas('workouts', fn ($q) => $q->where('is_active', true))
            ->count();

        $totalExercisesUsed = DB::table('exercise_workout')
            ->join('workouts', 'exercise_workout.workout_id', '=', 'workouts.id')
            ->where('workouts.trainer_id', $trainerId)
            ->distinct('exercise_workout.exercise_id')
            ->count('exercise_workout.exercise_id');

        return [
            'totalStudents' => Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))->count(),
            'activeStudents' => $activeStudents,
            'totalWorkouts' => DB::table('workouts')->where('trainer_id', $trainerId)->count(),
            'exercisesUsed' => $totalExercisesUsed,
            'newStudents' => $currentStudents,
            'newStudentsChange' => $this->calculateChange($currentStudents, $previousStudents),
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

    private function getStudentsByStatus(int $trainerId): array
    {
        $active = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))
            ->whereHas('workouts', fn ($q) => $q->where('is_active', true))
            ->count();

        $total = Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))->count();

        return [
            ['name' => 'Ativos', 'value' => $active, 'color' => '#10b981'],
            ['name' => 'Inativos', 'value' => $total - $active, 'color' => '#ef4444'],
        ];
    }

    private function getWorkoutsOverTime(int $trainerId, CarbonInterface $start, CarbonInterface $end): array
    {
        $days = $start->diffInDays($end);

        if ($days <= 31) {
            return DB::table('workouts')
                ->where('trainer_id', $trainerId)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
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
            return DB::table('workouts')
                ->where('trainer_id', $trainerId)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEARWEEK(created_at) as week, MIN(created_at) as date, COUNT(*) as count')
                ->groupBy('week')
                ->orderBy('date')
                ->get()
                ->map(fn ($row) => [
                    'date' => Carbon::parse($row->date)->format('d/m'),
                    'count' => (int) $row->count,
                ])
                ->all();
        }

        return DB::table('workouts')
            ->where('trainer_id', $trainerId)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::createFromFormat('Y-m', $row->month)->format('M/Y'),
                'count' => (int) $row->count,
            ])
            ->all();
    }

    private function getMostUsedExercises(int $trainerId, CarbonInterface $start, CarbonInterface $end): array
    {
        return DB::table('exercise_workout')
            ->join('exercises', 'exercise_workout.exercise_id', '=', 'exercises.id')
            ->join('workouts', 'exercise_workout.workout_id', '=', 'workouts.id')
            ->where('workouts.trainer_id', $trainerId)
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

    private function getExercisesByMuscleGroup(int $trainerId): array
    {
        $colors = ['#10b981', '#14b8a6', '#059669', '#0d9488', '#047857', '#065f46', '#064e3b', '#34d399'];

        return DB::table('exercise_workout')
            ->join('exercises', 'exercise_workout.exercise_id', '=', 'exercises.id')
            ->join('workouts', 'exercise_workout.workout_id', '=', 'workouts.id')
            ->where('workouts.trainer_id', $trainerId)
            ->selectRaw('exercises.muscle_group, COUNT(*) as count')
            ->groupBy('exercises.muscle_group')
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

    private function getWorkoutVolume(int $trainerId, CarbonInterface $start, CarbonInterface $end): array
    {
        $volume = DB::table('exercise_workout')
            ->join('workouts', 'exercise_workout.workout_id', '=', 'workouts.id')
            ->where('workouts.trainer_id', $trainerId)
            ->whereBetween('workouts.created_at', [$start, $end])
            ->selectRaw('
                COALESCE(SUM(exercise_workout.sets), 0) as total_sets,
                COALESCE(SUM(exercise_workout.reps), 0) as total_reps,
                COALESCE(SUM(exercise_workout.rest_seconds), 0) as total_rest
            ')
            ->first();

        $totalSets = (int) ($volume?->total_sets ?? 0);
        $totalReps = (int) ($volume?->total_reps ?? 0);
        $totalRest = (int) ($volume?->total_rest ?? 0);

        return [
            'totalSets' => $totalSets,
            'totalReps' => $totalReps,
            'totalRestMinutes' => round($totalRest / 60),
            'totalVolume' => $totalSets * $totalReps,
        ];
    }

    private function getStudentsTable(int $trainerId): array
    {
        return Client::whereHas('user', fn ($q) => $q->where('trainer_id', $trainerId))
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->map(fn ($client) => [
                'id' => $client->id,
                'name' => $client->user?->name ?? 'Sem nome',
                'nickname' => $client->nickname,
                'email' => $client->user?->email ?? '',
                'hasActiveWorkouts' => $client->workouts()->where('is_active', true)->exists(),
                'createdAt' => $client->created_at?->format('d/m/Y'),
            ])
            ->all();
    }
}
