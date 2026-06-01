<?php

namespace App\Actions\Dashboard;

use App\Models\Client;
use App\Models\ExerciseCompletion;
use App\Models\Trainer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GetClientDashboardStatsAction
{
    public function execute(User $user): array
    {
        $client = Client::where('user_id', $user->id)->first();

        $totalWorkouts = $client ? $client->workouts()->count() : 0;
        $completedWorkouts = $client ? $client->workouts()->whereNotNull('completed_at')->count() : 0;
        $currentStreak = $this->calculateStreak($user);
        $totalExercises = DB::table('exercises')->where('is_active', true)->count();

        return [
            'stats' => [
                'totalWorkouts' => $totalWorkouts,
                'completedWorkouts' => $completedWorkouts,
                'currentStreak' => $currentStreak,
                'totalExercises' => $totalExercises,
            ],
            'activeWorkout' => $this->getActiveWorkout($user),
            'completedWorkouts' => $this->getCompletedWorkouts($user),
            'weeklyWorkouts' => $this->getWeeklyWorkouts($user),
            'progressData' => $this->generateProgressData(),
            'nutritionData' => $this->generateNutritionData(),
            'bodyMetrics' => $this->generateBodyMetrics(),
            'upcomingWorkouts' => $this->getUpcomingWorkouts($user),
            'recentAchievements' => $this->getRecentAchievements($user),
            'trainer' => $this->getTrainerInfo($user),
        ];
    }

    private function getTrainerInfo(User $user): array
    {
        $trainerUser = $user->trainer;

        if (! $trainerUser) {
            return [
                'name' => 'Não atribuído',
                'specialty' => '—',
                'email' => '—',
            ];
        }

        $trainerProfile = Trainer::where('user_id', $trainerUser->id)->first();

        return [
            'name' => $trainerUser->name,
            'specialty' => $trainerProfile?->specialty ?? 'Personal Trainer',
            'email' => $trainerUser->email,
        ];
    }

    private function calculateStreak(User $user): int
    {
        try {
            $dates = ExerciseCompletion::where('user_id', $user->id)
                ->select(DB::raw('DATE(completed_at) as date'))
                ->groupBy(DB::raw('DATE(completed_at)'))
                ->orderByDesc('date')
                ->pluck('date')
                ->map(fn ($date) => Carbon::parse($date));
        } catch (\Throwable) {
            return 0;
        }

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $today = now()->startOfDay();

        foreach ($dates as $date) {
            $expected = $today->copy()->subDays($streak);
            if ($date->startOfDay()->eq($expected)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    private function getWeeklyWorkouts(User $user): array
    {
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];

        try {
            $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);

            $completionDates = ExerciseCompletion::where('user_id', $user->id)
                ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
                ->select(DB::raw('DATE(completed_at) as date'))
                ->groupBy(DB::raw('DATE(completed_at)'))
                ->pluck('date')
                ->map(fn ($date) => Carbon::parse($date)->startOfDay());
        } catch (\Throwable) {
            return collect($days)->map(fn ($day) => [
                'day' => $day,
                'completed' => false,
                'type' => 'Descanso',
            ])->all();
        }

        $weeklyWorkouts = [];

        foreach ($days as $i => $dayName) {
            $currentDay = $startOfWeek->copy()->addDays($i);
            $completed = $completionDates->contains(fn ($date) => $date->eq($currentDay));

            $weeklyWorkouts[] = [
                'day' => $dayName,
                'completed' => $completed,
                'type' => $completed ? 'Treino' : 'Descanso',
            ];
        }

        return $weeklyWorkouts;
    }

    private function getRecentAchievements(User $user): array
    {
        $client = Client::where('user_id', $user->id)->first();
        $totalCompleted = $client ? $client->workouts()->whereNotNull('completed_at')->count() : 0;
        $streak = $this->calculateStreak($user);

        $achievements = [];

        if ($totalCompleted >= 1) {
            $achievements[] = [
                'title' => "{$totalCompleted} treinos concluídos!",
                'description' => self::completionMessage($totalCompleted),
                'icon' => 'check-circle',
                'date' => 'Até agora',
            ];
        }

        if ($streak >= 3) {
            $achievements[] = [
                'title' => "{$streak} dias de sequência!",
                'description' => 'Continue assim!',
                'icon' => 'flame',
                'date' => 'Sequência atual',
            ];
        }

        if ($totalCompleted >= 5) {
            $achievements[] = [
                'title' => 'Dedicação!',
                'description' => '5+ treinos finalizados',
                'icon' => 'trophy',
                'date' => 'Meta alcançada',
            ];
        }

        if (empty($achievements)) {
            $achievements[] = [
                'title' => 'Primeiro treino!',
                'description' => 'Finalize seu primeiro treino',
                'icon' => 'target',
                'date' => 'Próximo passo',
            ];
        }

        return $achievements;
    }

    private static function completionMessage(int $count): string
    {
        return match (true) {
            $count >= 50 => 'Incrível!',
            $count >= 30 => 'Excelente progresso!',
            $count >= 20 => 'Continue firme!',
            $count >= 10 => 'Bom ritmo!',
            default => 'Ótimo começo!',
        };
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

    public function getActiveWorkout(User $user): ?array
    {
        $client = Client::where('user_id', $user->id)->first();

        if (! $client) {
            return null;
        }

        $workout = $client->workouts()
            ->with(['exercises.category', 'completions'])
            ->where('is_active', true)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if (! $workout) {
            return null;
        }

        $exercises = $workout->exercises->map(fn ($exercise) => [
            'id' => $exercise->id,
            'name' => $exercise->name,
            'category' => $exercise->category?->name,
            'pivot' => [
                'sets' => $exercise->pivot->sets,
                'reps' => $exercise->pivot->reps,
                'rest_seconds' => $exercise->pivot->rest_seconds,
            ],
            'completed' => $workout->completions
                ->where('user_id', $user->id)
                ->contains('exercise_id', $exercise->id),
        ]);

        $totalSeconds = $exercises->sum(fn ($ex) => $ex['pivot']['sets'] * ($ex['pivot']['reps'] * 3 + $ex['pivot']['rest_seconds']));

        return [
            'id' => $workout->id,
            'name' => $workout->name,
            'description' => $workout->description,
            'exercises_count' => $exercises->count(),
            'total_reps' => $exercises->sum(fn ($ex) => $ex['pivot']['sets'] * $ex['pivot']['reps']),
            'estimated_time_minutes' => max(1, round($totalSeconds / 60)),
            'is_active' => true,
            'exercises' => $exercises->values()->toArray(),
        ];
    }

    public function getUpcomingWorkouts(User $user): array
    {
        $client = Client::where('user_id', $user->id)->first();

        if (! $client) {
            return [];
        }

        return $client->workouts()
            ->withCount('exercises')
            ->where('is_active', true)
            ->whereNull('completed_at')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($workout) => [
                'id' => $workout->id,
                'name' => $workout->name,
                'date' => $workout->created_at->diffForHumans(),
                'time' => $workout->created_at->format('H:i'),
                'exercises' => $workout->exercises_count,
                'status' => 'scheduled',
            ])
            ->toArray();
    }

    public function getCompletedWorkouts(User $user): array
    {
        $client = Client::where('user_id', $user->id)->first();

        if (! $client) {
            return [];
        }

        return $client->workouts()
            ->withCount('exercises')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take(10)
            ->get()
            ->map(fn ($workout) => [
                'id' => $workout->id,
                'name' => $workout->name,
                'exercises' => $workout->exercises_count,
                'completed_at' => $workout->completed_at->diffForHumans(),
            ])
            ->toArray();
    }
}
