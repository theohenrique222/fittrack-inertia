<?php

namespace App\Actions\Dashboard;

use App\Actions\BodyMeasurements\BodyMetricsCalculator;
use App\Actions\BodyMeasurements\LatestBodyMeasurementAction;
use App\Models\Client;
use App\Models\ExerciseCompletion;
use App\Models\Trainer;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GetClientDashboardStatsAction
{
    public function __construct(
        private LatestBodyMeasurementAction $latestMeasurementAction,
    ) {}

    public function execute(User $user): array
    {
        $client = Client::where('user_id', $user->id)->first();

        $totalWorkouts = $client ? $client->workouts()->count() : 0;
        $completedWorkouts = $client
            ? WorkoutSession::where('client_id', $client->id)
                ->where('status', 'completed')
                ->distinct('workout_id')
                ->count('workout_id')
            : 0;
        $currentStreak = $this->calculateStreak($user);
        $totalExercises = DB::table('exercises')->where('is_active', true)->count();

        $latestMeasurement = $client ? $this->latestMeasurementAction->execute($client) : null;

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
            'progressData' => $this->getProgressData($client, $user),
            'nutritionData' => $latestMeasurement ? $this->getNutritionData($latestMeasurement) : $this->emptyNutritionData(),
            'bodyMetrics' => $latestMeasurement ? $this->getBodyMetrics($latestMeasurement) : [],
            'upcomingWorkouts' => $this->getUpcomingWorkouts($user),
            'recentAchievements' => $this->getRecentAchievements($user),
            'trainer' => $this->getTrainerInfo($user),
        ];
    }

    private function getBodyMetrics(array $latest): array
    {
        $metrics = $latest['metrics'];

        $items = [
            [
                'label' => 'Peso',
                'value' => $latest['weight'].' kg',
                'change' => '-',
                'trend' => 'up',
            ],
            [
                'label' => 'IMC',
                'value' => (string) $metrics['bmi']['value'],
                'change' => $metrics['bmi']['classification'],
                'trend' => 'up',
            ],
            [
                'label' => 'Gordura Corporal',
                'value' => $metrics['body_fat']['value'].'%',
                'change' => $metrics['body_fat']['classification'],
                'trend' => 'down',
            ],
            [
                'label' => 'Massa Magra',
                'value' => $metrics['lean_mass'].' kg',
                'change' => '-',
                'trend' => 'up',
            ],
        ];

        return $items;
    }

    private function getNutritionData(array $latest): array
    {
        $macros = $latest['metrics']['macros'];

        $tdee = $macros['calories'];

        return [
            'calories' => [
                'consumed' => 0,
                'target' => $tdee,
                'percentage' => 0,
            ],
            'protein' => [
                'consumed' => 0,
                'target' => $macros['protein']['grams'],
                'percentage' => 0,
                'unit' => 'g',
            ],
            'carbs' => [
                'consumed' => 0,
                'target' => $macros['carbs']['grams'],
                'percentage' => 0,
                'unit' => 'g',
            ],
            'fat' => [
                'consumed' => 0,
                'target' => $macros['fat']['grams'],
                'percentage' => 0,
                'unit' => 'g',
            ],
        ];
    }

    private function emptyNutritionData(): array
    {
        return [
            'calories' => ['consumed' => 0, 'target' => 2400, 'percentage' => 0],
            'protein' => ['consumed' => 0, 'target' => 150, 'percentage' => 0, 'unit' => 'g'],
            'carbs' => ['consumed' => 0, 'target' => 300, 'percentage' => 0, 'unit' => 'g'],
            'fat' => ['consumed' => 0, 'target' => 80, 'percentage' => 0, 'unit' => 'g'],
        ];
    }

    private function getProgressData(?Client $client, User $user): array
    {
        if (! $client) {
            return [];
        }

        $measurements = $client->bodyMeasurements()
            ->orderBy('recorded_at', 'asc')
            ->take(8)
            ->get(['weight', 'neck', 'waist', 'hip', 'recorded_at']);

        if ($measurements->isEmpty()) {
            return [];
        }

        return $measurements->map(function ($m) use ($client) {
            $bodyFat = 0;

            if ($m->neck && $m->waist) {
                try {
                    $calculator = new BodyMetricsCalculator($m, $client->user);
                    $fullMetrics = $calculator->calculate();
                    $bodyFat = $fullMetrics['body_fat']['value'];
                } catch (\Throwable) {
                }
            }

            return [
                'week' => $m->recorded_at->format('d/m'),
                'weight' => (float) $m->weight,
                'bodyFat' => $bodyFat,
            ];
        })->toArray();
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
        $totalCompleted = $client
            ? WorkoutSession::where('client_id', $client->id)
                ->where('status', 'completed')
                ->distinct('workout_id')
                ->count('workout_id')
            : 0;
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

    public function getActiveWorkout(User $user): ?array
    {
        $client = Client::where('user_id', $user->id)->first();

        if (! $client) {
            return null;
        }

        $workout = $client->workouts()
            ->with(['exercises.category'])
            ->where('is_active', true)
            ->latest()
            ->first();

        if (! $workout) {
            return null;
        }

        $activeSession = WorkoutSession::where('workout_id', $workout->id)
            ->where('client_id', $client->id)
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        $sessionCompletions = collect();
        if ($activeSession) {
            $sessionCompletions = ExerciseCompletion::where('workout_session_id', $activeSession->id)
                ->where('user_id', $user->id)
                ->get();
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
            'completed' => $sessionCompletions->contains('exercise_id', $exercise->id),
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

        $latestCompleted = DB::table('workout_sessions')
            ->select('workout_id', DB::raw('MAX(completed_at) as last_completed_at'))
            ->where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->groupBy('workout_id')
            ->orderByDesc('last_completed_at')
            ->take(10)
            ->get();

        if ($latestCompleted->isEmpty()) {
            return [];
        }

        $workoutIds = $latestCompleted->pluck('workout_id');

        /** @var Collection<int, Workout> $workouts */
        $workouts = Workout::whereIn('id', $workoutIds)
            ->withCount('exercises')
            ->get()
            ->keyBy('id');

        return $latestCompleted->map(fn ($item) => [
            'id' => $item->workout_id,
            'name' => $workouts[$item->workout_id]->name,
            'exercises' => $workouts[$item->workout_id]->exercises_count,
            'completed_at' => Carbon::parse($item->last_completed_at)->diffForHumans(),
        ])->toArray();
    }
}
