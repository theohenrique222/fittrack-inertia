<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Check, Dumbbell, Flame, Trophy, Target, CheckCircle, Calendar, Clock, ArrowUpRight, ArrowDownRight, ChevronRight, Play, Repeat, Scale } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import ProgressRing from '@/components/dashboard/ProgressRing.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { students } from '@/routes';

interface ExercisePivot {
    sets: number;
    reps: number;
    rest_seconds: number;
}

interface DialogExercise {
    id: number;
    name: string;
    category?: string | null;
    pivot: ExercisePivot;
    completed?: boolean;
}

interface ActiveWorkout {
    id: number;
    name: string;
    description?: string;
    exercises_count: number;
    total_reps: number;
    estimated_time_minutes: number;
    is_active: boolean;
    exercises?: DialogExercise[];
}

interface CompletedWorkout {
    id: number;
    name: string;
    exercises: number;
    completed_at: string;
}

interface UpcomingWorkout {
    id?: number;
    name: string;
    date: string;
    time: string;
    exercises: number;
    status: string;
}

interface DialogWorkoutData {
    id: number;
    name: string;
    description?: string;
    exercises_count: number;
    total_reps?: number;
    estimated_time_minutes?: number;
    exercises?: DialogExercise[];
}

const page = usePage();

interface Props {
    stats: {
        totalWorkouts: number;
        completedWorkouts: number;
        currentStreak: number;
        totalExercises: number;
    };
    weeklyWorkouts: { day: string; completed: boolean; type: string }[];
    progressData: { week: string; weight: number; bodyFat: number }[];
    nutritionData: {
        calories: { consumed: number; target: number; percentage: number };
        protein: { consumed: number; target: number; percentage: number; unit: string };
        carbs: { consumed: number; target: number; percentage: number; unit: string };
        fat: { consumed: number; target: number; percentage: number; unit: string };
    };
    bodyMetrics: { label: string; value: string; change: string; trend: string }[];
    activeWorkout: ActiveWorkout | null;
    completedWorkouts: CompletedWorkout[];
    upcomingWorkouts: UpcomingWorkout[];
    recentAchievements: { title: string; description: string; icon: string; date: string }[];
    trainer: { name: string; specialty: string; email: string };
    hasTodayMeasurement: boolean;
    clientId?: number;
}

const props = defineProps<Props>();

const iconMap: Record<string, any> = {
    'flame': Flame,
    'trophy': Trophy,
    'target': Target,
    'check-circle': CheckCircle,
};

const isDialogOpen = ref(false);
const dialogWorkout = ref<DialogWorkoutData | null>(null);

function openWorkoutDialog(workout: ActiveWorkout | UpcomingWorkout) {
    if (!workout.id) {
return;
}

    if ('exercises' in workout && workout.exercises) {
        dialogWorkout.value = {
            id: workout.id,
            name: workout.name,
            description: workout.description,
            exercises_count: workout.exercises_count,
            total_reps: workout.total_reps,
            estimated_time_minutes: workout.estimated_time_minutes,
            exercises: workout.exercises,
        };
    } else {
        dialogWorkout.value = {
            id: workout.id,
            name: workout.name,
            exercises_count: workout.exercises,
        };
    }

    isDialogOpen.value = true;
}

function formatRest(seconds: number): string {
    if (seconds >= 60) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;

        return secs > 0 ? `${mins}m${secs}s` : `${mins}min`;
    }

    return `${seconds}s`;
}

function startWorkout() {
    if (dialogWorkout.value) {
        isDialogOpen.value = false;
        router.visit(`/workouts/${dialogWorkout.value.id}`);
    }
}

const completionRate = computed(() => {
    if (props.stats.totalWorkouts === 0) {
return 0;
    }

    return Math.round((props.stats.completedWorkouts / props.stats.totalWorkouts) * 100);
});

const progressChartData = computed(() => {
    return props.progressData.map((d) => ({
        month: d.week,
        students: d.weight,
        workouts: d.bodyFat * 5,
    }));
});

const completedThisWeek = computed(() => {
    return props.weeklyWorkouts.filter((w) => w.completed).length;
});
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Welcome Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 md:p-8 text-white shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32" />
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24" />
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold">
                    Meu Progresso
                </h1>
                <p class="text-sm md:text-base opacity-90 mt-2 max-w-xl">
                    Continue assim! Você está no caminho certo para alcançar seus objetivos 💪
                </p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <Flame class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ stats.currentStreak }} dias de sequência</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <CheckCircle class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ completedThisWeek }}/{{ weeklyWorkouts.length }} esta semana</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Treinos Total</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalWorkouts }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <ArrowUpRight class="w-4 h-4 text-emerald-500" />
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ completionRate }}%</span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">concluídos</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <Dumbbell class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Sequência Atual</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.currentStreak }} dias</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <Flame class="w-4 h-4 text-orange-500" />
                            <span class="text-xs font-medium text-orange-600 dark:text-orange-400">Recorde!</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <Flame class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Concluídos</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.completedWorkouts }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <ArrowDownRight class="w-4 h-4 text-neutral-400" />
                            <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ stats.totalWorkouts - stats.completedWorkouts }} restantes</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <CheckCircle class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Exercícios</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalExercises }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Disponíveis</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <Target class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Workout -->
        <div
            v-if="activeWorkout"
            class="group rounded-xl border-2 border-emerald-400 dark:border-emerald-600 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 p-5 shadow-sm hover:shadow-lg transition-all cursor-pointer"
            @click="openWorkoutDialog(activeWorkout)"
        >
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform pointer-events-none">
                        <Dumbbell class="w-7 h-7" />
                    </div>
                    <div class="pointer-events-none">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium uppercase tracking-wide">Treino Ativo</p>
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ activeWorkout.name }}
                        </h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ activeWorkout.exercises_count }} exercícios
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 pointer-events-none">
                    <span class="text-sm font-medium">Ver treino</span>
                    <ChevronRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                </div>
            </div>
        </div>

        <!-- Completed Workouts -->
        <div v-if="completedWorkouts.length" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <CheckCircle class="h-5 w-5 text-emerald-500" />
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Treinos Concluídos</h3>
                </div>
                <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                    {{ completedWorkouts.length }} total
                </span>
            </div>
            <div class="space-y-1">
                <div
                    v-for="workout in completedWorkouts"
                    :key="workout.id"
                    class="flex items-center gap-3 rounded-lg border border-emerald-100 dark:border-emerald-800/50 bg-emerald-50/50 dark:bg-emerald-900/10 px-4 py-3"
                >
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                        <Check class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">
                            {{ workout.name }}
                        </p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ workout.exercises }} exercícios · {{ workout.completed_at }}
                        </p>
                    </div>
                    <Link
                        :href="`/workouts/${workout.id}`"
                        class="shrink-0 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"
                    >
                        Ver
                    </Link>
                </div>
            </div>
        </div>

        <!-- Body Metrics -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Métricas Corporais</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="metric in bodyMetrics"
                    :key="metric.label"
                    class="group rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 p-4 hover:border-emerald-300 dark:hover:border-emerald-600 transition-all duration-200"
                >
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ metric.label }}</p>
                    <p class="text-2xl font-bold mt-1 text-neutral-900 dark:text-white">{{ metric.value }}</p>
                    <div class="flex items-center gap-1 mt-1">
                        <ArrowUpRight v-if="metric.trend === 'up'" class="w-3 h-3 text-emerald-500" />
                        <ArrowDownRight v-else class="w-3 h-3 text-emerald-500" />
                        <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            {{ metric.change }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Workouts -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-neutral-900 dark:text-white">Treinos da Semana</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                    {{ completedThisWeek }}/{{ weeklyWorkouts.length }} concluídos
                </span>
            </div>
            <div class="grid grid-cols-7 gap-2 md:gap-3">
                <div
                    v-for="workout in weeklyWorkouts"
                    :key="workout.day"
                    class="flex flex-col items-center gap-2 p-3 md:p-4 rounded-xl transition-all duration-200"
                    :class="workout.completed
                        ? 'bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800'
                        : workout.type === 'Descanso'
                            ? 'bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700'
                            : 'bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800'"
                >
                    <span class="text-xs font-semibold text-neutral-700 dark:text-neutral-300">{{ workout.day }}</span>
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm"
                        :class="workout.completed
                            ? 'bg-emerald-500 text-white'
                            : workout.type === 'Descanso'
                                ? 'bg-neutral-200 dark:bg-neutral-600 text-neutral-500 dark:text-neutral-300'
                                : 'bg-orange-500 text-white'"
                    >
                        <CheckCircle v-if="workout.completed" class="w-5 h-5" />
                        <span v-else class="text-sm font-bold">{{ workout.type.charAt(0) }}</span>
                    </div>
                    <span class="text-xs text-neutral-500 dark:text-neutral-400 text-center leading-tight">
                        {{ workout.type }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Nutrition & Progress Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Nutrition -->
            <div v-if="hasTodayMeasurement" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Nutrição do Dia</h3>
                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                        {{ nutritionData.calories.consumed }} / {{ nutritionData.calories.target }} kcal
                    </span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800">
                        <ProgressRing
                            :percentage="nutritionData.calories.percentage"
                            :size="60"
                            :stroke-width="5"
                            color="#10b981"
                        >
                            <span class="text-xs font-bold">{{ nutritionData.calories.percentage }}%</span>
                        </ProgressRing>
                        <div>
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">Calorias</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ nutritionData.calories.consumed }} kcal consumidas
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="flex flex-col items-center gap-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                            <ProgressRing
                                :percentage="nutritionData.protein.percentage"
                                :size="50"
                                :stroke-width="4"
                                color="#3b82f6"
                            >
                                <span class="text-xs font-bold">{{ nutritionData.protein.consumed }}{{ nutritionData.protein.unit }}</span>
                            </ProgressRing>
                            <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Proteína</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800">
                            <ProgressRing
                                :percentage="nutritionData.carbs.percentage"
                                :size="50"
                                :stroke-width="4"
                                color="#f59e0b"
                            >
                                <span class="text-xs font-bold">{{ nutritionData.carbs.consumed }}{{ nutritionData.carbs.unit }}</span>
                            </ProgressRing>
                            <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Carbos</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800">
                            <ProgressRing
                                :percentage="nutritionData.fat.percentage"
                                :size="50"
                                :stroke-width="4"
                                color="#f43f5e"
                            >
                                <span class="text-xs font-bold">{{ nutritionData.fat.consumed }}{{ nutritionData.fat.unit }}</span>
                            </ProgressRing>
                            <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">Gordura</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nutrition CTA (no measurement today) -->
            <div v-else class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex flex-col items-center justify-center h-full min-h-[280px] text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-lg mb-4">
                        <Scale class="w-8 h-8" />
                    </div>
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-2">Medidas do Dia</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 max-w-xs mb-6">
                        Registre suas medidas corporais de hoje para acompanhar sua evolução e receber suas metas nutricionais.
                    </p>
                    <Link
                        v-if="clientId"
                        :href="students.measurements.url({ student: clientId })"
                    >
                        <Button>
                            <Scale class="w-4 h-4 mr-2" />
                            Registrar Medidas
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Evolução de Peso</h3>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-500" />
                            <span class="text-xs text-neutral-600 dark:text-neutral-400">Peso</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-teal-400" />
                            <span class="text-xs text-neutral-600 dark:text-neutral-400">Gordura x5</span>
                        </div>
                    </div>
                </div>
                <LineChart :data="progressChartData" :height="200" />
            </div>
        </div>

        <!-- Upcoming Workouts & Achievements -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Upcoming Workouts -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Próximos Treinos</h3>
                    <Link
                        href="/me/workouts"
                        class="text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"
                    >
                        Ver todos
                    </Link>
                </div>
                <div class="space-y-1">
                    <div
                        v-for="(workout, index) in upcomingWorkouts"
                        :key="index"
                        class="flex items-center justify-between py-3 px-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors group cursor-pointer"
                        @click="openWorkoutDialog(workout)"
                    >
                        <div class="flex items-center gap-3 pointer-events-none">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-sm group-hover:scale-110 transition-transform">
                                <Dumbbell class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                    {{ workout.name }}
                                </p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400 flex items-center gap-1">
                                        <Calendar class="w-3 h-3" />
                                        {{ workout.date }}
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400 flex items-center gap-1">
                                        <Clock class="w-3 h-3" />
                                        {{ workout.time }}
                                    </span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ workout.exercises }} exercícios
                                    </span>
                                </div>
                            </div>
                        </div>
                        <ChevronRight v-if="workout.id" class="w-5 h-5 text-neutral-300 dark:text-neutral-600 group-hover:text-emerald-500 transition-colors pointer-events-none" />
                    </div>
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Conquistas Recentes</h3>
                <div class="space-y-1">
                    <div
                        v-for="(achievement, index) in recentAchievements"
                        :key="index"
                        class="flex items-start gap-3 py-3 px-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white flex-shrink-0 shadow-sm">
                            <component :is="iconMap[achievement.icon]" class="w-5 h-5" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ achievement.title }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ achievement.description }}</p>
                            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">{{ achievement.date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trainer Info -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                    {{ trainer.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium uppercase tracking-wide">Seu Personal Trainer</p>
                    <p class="text-lg font-bold text-neutral-900 dark:text-white">{{ trainer.name }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ trainer.specialty }}</p>
                </div>
            </div>
        </div>
    </div>

    <Dialog v-model:open="isDialogOpen">
        <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto p-0 gap-0">
            <!-- Hero Header -->
            <div v-if="dialogWorkout" class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 pt-8 pb-6 text-white">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/20"></div>
                    <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/10"></div>
                </div>

                <div class="relative">
                    <div class="flex items-center gap-2 mb-1">
                        <Dumbbell class="h-4 w-4 text-emerald-200" />
                        <span class="text-xs font-medium uppercase tracking-wider text-emerald-200">Resumo do Treino</span>
                    </div>

                    <DialogTitle class="text-xl font-bold mt-1">
                        {{ dialogWorkout.name }}
                    </DialogTitle>

                    <DialogDescription v-if="dialogWorkout.description" class="text-sm text-emerald-100 mt-2">
                        {{ dialogWorkout.description }}
                    </DialogDescription>

                    <!-- Stats Row -->
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-xs backdrop-blur-sm">
                            <Dumbbell class="h-3.5 w-3.5" />
                            <span>{{ dialogWorkout.exercises_count }} exercícios</span>
                        </div>
                        <div v-if="dialogWorkout.total_reps" class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-xs backdrop-blur-sm">
                            <Repeat class="h-3.5 w-3.5" />
                            <span>{{ dialogWorkout.total_reps }} reps</span>
                        </div>
                        <div v-if="dialogWorkout.estimated_time_minutes" class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-xs backdrop-blur-sm">
                            <Clock class="h-3.5 w-3.5" />
                            <span>~{{ dialogWorkout.estimated_time_minutes }}min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                <!-- Muscle Groups -->
                <div v-if="dialogWorkout?.exercises?.length" class="mb-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">Grupos Musculares</h4>
                    <div class="flex flex-wrap gap-1.5">
                        <span
                            v-for="group in [...new Set(dialogWorkout.exercises.map(e => e.category).filter(Boolean))]"
                            :key="group"
                            class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300"
                        >
                            {{ group }}
                        </span>
                        <span
                            v-if="![...new Set(dialogWorkout.exercises.map(e => e.category).filter(Boolean))].length"
                            class="text-xs text-neutral-400 dark:text-neutral-500"
                        >
                            Diversos grupos
                        </span>
                    </div>
                </div>

                <!-- Exercise List -->
                <div v-if="dialogWorkout?.exercises?.length" class="space-y-1.5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2">
                        Exercícios
                        <span class="font-normal text-neutral-400 dark:text-neutral-500">· {{ dialogWorkout.exercises.length }}</span>
                    </h4>
                    <div
                        v-for="(exercise, index) in dialogWorkout.exercises"
                        :key="exercise.id"
                        :class="[
                            'flex items-center gap-3 rounded-lg border px-3 py-2.5',
                            exercise.completed
                                ? 'border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20'
                                : 'border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50',
                        ]"
                    >
                        <div :class="[
                            'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                            exercise.completed
                                ? 'bg-emerald-500 text-white'
                                : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                        ]">
                            <Check v-if="exercise.completed" class="h-4 w-4" />
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ exercise.name }}</p>
                            <div class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                <span>{{ exercise.pivot.sets }} × {{ exercise.pivot.reps }}</span>
                                <span class="text-neutral-300 dark:text-neutral-600">·</span>
                                <span>{{ formatRest(exercise.pivot.rest_seconds) }} descanso</span>
                            </div>
                        </div>
                        <span
                            v-if="exercise.category"
                            class="shrink-0 rounded-md bg-neutral-100 dark:bg-neutral-700 px-1.5 py-0.5 text-[10px] font-medium text-neutral-500 dark:text-neutral-400"
                        >
                            {{ exercise.category }}
                        </span>
                    </div>
                </div>

                <div v-else-if="dialogWorkout" class="flex flex-col items-center justify-center py-6 text-center">
                    <Dumbbell class="h-8 w-8 text-neutral-300 dark:text-neutral-600 mb-2" />
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Nenhum exercício neste treino</p>
                </div>
            </div>

            <DialogFooter class="px-6 pb-6 pt-2 gap-2">
                <button
                    class="flex-1 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-4 py-2.5 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors"
                    @click="isDialogOpen = false"
                >
                    Fechar
                </button>
                <button
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm"
                    @click="startWorkout"
                >
                    <Play class="h-4 w-4" />
                    Iniciar Treino
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
