<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Check, Dumbbell, Flame, Trophy, Target, CheckCircle, Calendar, Clock, ArrowUpRight, Play, Repeat, Scale, ChevronRight, Zap, Activity, Sparkles, TrendingUp, BarChart3, ListOrdered } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import ProgressRing from '@/components/dashboard/ProgressRing.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { workouts } from '@/routes/me';
import students from '@/routes/students';

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

interface Props {
    userName: string;
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
            description: (workout as ActiveWorkout).description,
            exercises_count: (workout as ActiveWorkout).exercises_count,
            total_reps: (workout as ActiveWorkout).total_reps,
            estimated_time_minutes: (workout as ActiveWorkout).estimated_time_minutes,
            exercises: workout.exercises,
        };
    } else {
        dialogWorkout.value = {
            id: workout.id,
            name: workout.name,
            exercises_count: (workout as UpcomingWorkout).exercises,
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
        workouts: d.bodyFat,
    }));
});

const completedThisWeek = computed(() => {
    return props.weeklyWorkouts.filter((w) => w.completed).length;
});

const hasProgressData = computed(() => props.progressData.length > 1);

const statCards = computed(() => [
    {
        label: 'Treinos Total',
        value: props.stats.totalWorkouts,
        icon: Dumbbell,
        color: 'from-emerald-500 to-emerald-600',
        bg: 'bg-emerald-50 dark:bg-emerald-900/20',
        text: 'text-emerald-600 dark:text-emerald-400',
        href: workouts.url(),
    },
    {
        label: 'Sequência',
        value: `${props.stats.currentStreak}d`,
        icon: Flame,
        color: 'from-orange-500 to-rose-500',
        bg: 'bg-orange-50 dark:bg-orange-900/20',
        text: 'text-orange-600 dark:text-orange-400',
    },
    {
        label: 'Concluídos',
        value: props.stats.completedWorkouts,
        icon: CheckCircle,
        color: 'from-teal-500 to-teal-600',
        bg: 'bg-teal-50 dark:bg-teal-900/20',
        text: 'text-teal-600 dark:text-teal-400',
    },
    {
        label: 'Disponíveis',
        value: props.stats.totalExercises,
        icon: BarChart3,
        color: 'from-violet-500 to-violet-600',
        bg: 'bg-violet-50 dark:bg-violet-900/20',
        text: 'text-violet-600 dark:text-violet-400',
    },
]);

</script>

<template>
    <div class="flex flex-col gap-5 pb-8">
        <!-- ========== HERO SECTION ========== -->
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 md:p-8 text-white shadow-lg">
            <div class="pointer-events-none absolute -top-16 -right-16 h-48 w-48 rounded-full bg-white/10 blur-xl" />
            <div class="pointer-events-none absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/5 blur-lg" />
            <div class="pointer-events-none absolute top-1/2 left-1/3 h-64 w-64 rounded-full bg-emerald-400/20 blur-3xl" />

            <div class="relative z-10">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 text-emerald-100">
                            <Sparkles class="h-4 w-4" />
                            <span class="text-xs font-medium uppercase tracking-wider">Painel do Aluno</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ userName }}
                        </h1>
                        <p class="text-sm md:text-base text-emerald-50/90 max-w-xl">
                            Continue assim! Você está no caminho certo para alcançar seus objetivos
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center gap-1 bg-white/15 backdrop-blur-sm rounded-full px-4 py-2">
                        <Activity class="h-4 w-4 text-emerald-200" />
                        <span class="text-xs font-semibold">{{ completionRate }}% concluído</span>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-sm px-4 py-2 text-sm font-medium">
                        <Flame class="h-4 w-4 text-orange-200" />
                        <span>{{ stats.currentStreak }} dias de sequência</span>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/20 backdrop-blur-sm px-4 py-2 text-sm font-medium">
                        <CheckCircle class="h-4 w-4 text-emerald-200" />
                        <span>{{ completedThisWeek }}/{{ weeklyWorkouts.length }} esta semana</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== STATS ROW ========== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div
                v-for="card in statCards"
                :key="card.label"
                class="group relative overflow-hidden rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-4 shadow-xs hover:shadow-md transition-all duration-300 hover:-translate-y-0.5"
                :class="card.href ? 'cursor-pointer' : ''"
                @click="card.href ? router.visit(card.href) : undefined"
            >
                <div class="flex items-start justify-between">
                    <div class="space-y-1.5">
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ card.label }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ card.value }}</p>
                    </div>
                    <div :class="[
                        'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-xs',
                        card.bg,
                    ]">
                        <component :is="card.icon" :class="['h-5 w-5', card.text]" />
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-linear-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    :class="card.color" />
            </div>
        </div>

        <!-- ========== ACTIVE WORKOUT / TODAY'S SPOTLIGHT ========== -->
        <div
            v-if="activeWorkout"
            class="group relative overflow-hidden rounded-xl border-2 border-emerald-400/50 dark:border-emerald-600/50 bg-linear-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 p-5 shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer"
            @click="openWorkoutDialog(activeWorkout)"
        >
            <div class="pointer-events-none absolute top-0 right-0 h-32 w-32 rounded-full bg-emerald-400/10 blur-2xl" />
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-emerald-500 to-teal-500 text-white shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <Zap class="h-7 w-7" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                                <Activity class="h-3 w-3" />
                                Treino Ativo
                            </span>
                        </div>
                        <h3 class="mt-1.5 text-lg font-bold text-neutral-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ activeWorkout.name }}
                        </h3>
                        <div class="mt-1 flex items-center gap-3 text-sm text-neutral-500 dark:text-neutral-400">
                            <span class="flex items-center gap-1">
                                <Dumbbell class="h-3.5 w-3.5" />
                                {{ activeWorkout.exercises_count }} exercícios
                            </span>
                            <span class="text-neutral-300 dark:text-neutral-600">·</span>
                            <span class="flex items-center gap-1">
                                <Clock class="h-3.5 w-3.5" />
                                ~{{ activeWorkout.estimated_time_minutes }}min
                            </span>
                        </div>
                    </div>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                    <span class="text-sm font-medium">Continuar</span>
                    <ChevronRight class="h-5 w-5 group-hover:translate-x-1 transition-transform" />
                </div>
            </div>
        </div>

        <!-- ========== BODY METRICS BAR ========== -->
        <div v-if="bodyMetrics.length > 0" class="rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-5 shadow-xs">
            <div class="flex items-center gap-2 mb-4">
                <Activity class="h-4 w-4 text-emerald-500" />
                <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Métricas Corporais</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div
                    v-for="metric in bodyMetrics"
                    :key="metric.label"
                    class="rounded-lg bg-neutral-50 dark:bg-neutral-800/50 border border-neutral-100 dark:border-neutral-700/50 p-3 hover:border-emerald-200 dark:hover:border-emerald-700/50 transition-colors"
                >
                    <p class="text-[11px] font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ metric.label }}</p>
                    <p class="mt-1 text-xl font-bold text-neutral-900 dark:text-white">{{ metric.value }}</p>
                    <div class="mt-0.5 flex items-center gap-1">
                        <ArrowUpRight v-if="metric.trend === 'up'" class="h-3 w-3 text-emerald-500" />
                        <ArrowUpRight v-else class="h-3 w-3 text-rose-500 rotate-180" />
                        <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ metric.change }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== WEEKLY WORKOUT CALENDAR ========== -->
        <div class="rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <Calendar class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-emerald-500" />
                    <h3 class="text-xs sm:text-sm font-semibold text-neutral-900 dark:text-white">Semana</h3>
                </div>
                <Badge variant="secondary" class="text-[10px] sm:text-xs">
                    {{ completedThisWeek }}/{{ weeklyWorkouts.length }} concluídos
                </Badge>
            </div>
            <div class="grid grid-cols-7 gap-1 sm:gap-2">
                <div
                    v-for="workout in weeklyWorkouts"
                    :key="workout.day"
                    class="flex flex-col items-center gap-1 sm:gap-1.5 py-1.5 sm:py-2.5 rounded-lg sm:rounded-xl transition-all duration-200"
                    :class="workout.completed
                        ? 'bg-emerald-50 dark:bg-emerald-900/20'
                        : workout.type === 'Descanso'
                            ? 'bg-neutral-50 dark:bg-neutral-800/50'
                            : 'bg-amber-50 dark:bg-amber-900/10'"
                >
                    <span class="text-[10px] sm:text-[11px] font-semibold text-neutral-600 dark:text-neutral-400 uppercase">{{ workout.day }}</span>
                    <div class="relative flex items-center justify-center">
                        <div
                            class="h-7 w-7 sm:h-9 sm:w-9 rounded-full flex items-center justify-center shadow-xs transition-transform duration-200 hover:scale-110"
                            :class="workout.completed
                                ? 'bg-emerald-500 text-white'
                                : workout.type === 'Descanso'
                                    ? 'bg-neutral-200 dark:bg-neutral-600 text-neutral-400 dark:text-neutral-300'
                                    : 'bg-amber-500 text-white'"
                        >
                            <Check v-if="workout.completed" class="h-3 w-3 sm:h-4 sm:w-4" />
                            <span v-else class="text-[10px] sm:text-xs font-bold">{{ workout.type === 'Descanso' ? '—' : '!' }}</span>
                        </div>
                    </div>
                    <span class="text-[9px] sm:text-[10px] text-neutral-500 dark:text-neutral-400 font-medium leading-tight text-center">
                        {{ workout.type }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ========== PROGRESS CHART + NUTRITION ROW ========== -->
        <div class="grid gap-4 lg:grid-cols-5">
            <!-- Weight Progress Chart -->
            <div class="lg:col-span-3 rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <TrendingUp class="h-4 w-4 text-emerald-500" />
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Evolução</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <div class="h-2.5 w-2.5 rounded-full bg-emerald-500" />
                            <span class="text-[11px] text-neutral-500 dark:text-neutral-400">Peso</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="h-2.5 w-2.5 rounded-full bg-teal-400" />
                            <span class="text-[11px] text-neutral-500 dark:text-neutral-400">Gordura</span>
                        </div>
                    </div>
                </div>
                <div v-if="hasProgressData" class="w-full">
                    <LineChart :data="progressChartData" :height="200" />
                </div>
                <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 mb-3">
                        <BarChart3 class="h-6 w-6 text-emerald-500" />
                    </div>
                    <p class="text-sm font-medium text-neutral-900 dark:text-white mb-1">Nenhum dado ainda</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Registre medidas para ver seu progresso</p>
                </div>
            </div>

            <!-- Nutrition / CTA -->
            <div class="lg:col-span-2 rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-4">
                    <Zap class="h-4 w-4 text-emerald-500" />
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Nutrição do Dia</h3>
                </div>

                <div v-if="hasTodayMeasurement" class="space-y-3">
                    <div class="flex items-center gap-3 rounded-lg bg-neutral-50 dark:bg-neutral-800/50 p-3">
                        <ProgressRing
                            :percentage="nutritionData.calories.percentage"
                            :size="52"
                            :stroke-width="4"
                            color="#10b981"
                        >
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400">{{ nutritionData.calories.percentage }}%</span>
                        </ProgressRing>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">Calorias</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                {{ nutritionData.calories.consumed }} / {{ nutritionData.calories.target }} kcal
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="flex flex-col items-center gap-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/10 p-2.5">
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ nutritionData.protein.consumed }}g</span>
                            <span class="text-[10px] text-neutral-500 dark:text-neutral-400">Proteína</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 p-2.5">
                            <span class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ nutritionData.carbs.consumed }}g</span>
                            <span class="text-[10px] text-neutral-500 dark:text-neutral-400">Carbos</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/10 p-2.5">
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ nutritionData.fat.consumed }}g</span>
                            <span class="text-[10px] text-neutral-500 dark:text-neutral-400">Gordura</span>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-linear-to-br from-emerald-500 to-teal-500 text-white shadow-sm mb-3">
                        <Scale class="h-6 w-6" />
                    </div>
                    <p class="text-sm font-medium text-neutral-900 dark:text-white mb-1">Medidas do Dia</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mb-4 max-w-[200px]">
                        Registre suas medidas para liberar as metas nutricionais
                    </p>
                    <Link
                        v-if="clientId"
                        :href="students.measurements.url({ student: clientId })"
                    >
                        <Button size="sm">
                            <Scale class="h-3.5 w-3.5 mr-1.5" />
                            Registrar
                        </Button>
                    </Link>
                </div>
            </div>
        </div>

        <!-- ========== BOTTOM GRID ========== -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Upcoming Workouts -->
            <div v-if="upcomingWorkouts.length" class="rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <ListOrdered class="h-4 w-4 text-emerald-500" />
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Próximos</h3>
                    </div>
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
                        class="flex items-center justify-between rounded-lg px-3 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors group cursor-pointer"
                        @click="openWorkoutDialog(workout)"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-linear-to-br from-emerald-500 to-teal-500 text-white shadow-xs">
                                <Dumbbell class="h-5 w-5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-neutral-900 dark:text-white truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                    {{ workout.name }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400 flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        {{ workout.date }}
                                    </span>
                                    <span class="text-xs text-neutral-300 dark:text-neutral-600">·</span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ workout.exercises }} ex</span>
                                </div>
                            </div>
                        </div>
                        <ChevronRight class="h-4 w-4 shrink-0 text-neutral-300 dark:text-neutral-600 group-hover:text-emerald-500 transition-colors" />
                    </div>
                </div>
            </div>

            <!-- Achievements + Trainer -->
            <div class="rounded-xl border border-emerald-100/50 dark:border-emerald-900/20 bg-white dark:bg-neutral-900 p-5 shadow-xs">
                <div class="flex items-center gap-2 mb-4">
                    <Trophy class="h-4 w-4 text-emerald-500" />
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Conquistas</h3>
                </div>

                <div class="space-y-2">
                    <div
                        v-for="(achievement, index) in recentAchievements"
                        :key="index"
                        class="flex items-start gap-3 rounded-lg px-3 py-2.5 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors"
                    >
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-linear-to-br from-emerald-500 to-teal-500 text-white shadow-xs">
                            <component :is="iconMap[achievement.icon] || Trophy" class="h-4 w-4" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ achievement.title }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ achievement.description }}</p>
                        </div>
                        <span class="shrink-0 text-[10px] text-neutral-400 dark:text-neutral-500">{{ achievement.date }}</span>
                    </div>
                </div>

                <!-- Trainer Mini Card -->
                <div class="mt-4 flex items-center gap-3 rounded-lg bg-linear-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/10 dark:to-teal-900/10 p-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-emerald-500 to-teal-500 text-white text-sm font-bold shadow-xs">
                        {{ trainer.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Personal</p>
                        <p class="text-sm font-semibold text-neutral-900 dark:text-white truncate">{{ trainer.name }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ trainer.specialty }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== WORKOUT DETAIL DIALOG ========== -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto p-0 gap-0">
                <div v-if="dialogWorkout" class="relative overflow-hidden bg-linear-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 pt-8 pb-6 text-white">
                    <div class="pointer-events-none absolute inset-0 opacity-10">
                        <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/20" />
                        <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/10" />
                    </div>

                    <div class="relative">
                        <div class="flex items-center gap-2 mb-1">
                            <Dumbbell class="h-4 w-4 text-emerald-200" />
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-200">Resumo do Treino</span>
                        </div>

                        <DialogTitle class="text-xl font-bold mt-1">
                            {{ dialogWorkout.name }}
                        </DialogTitle>

                        <DialogDescription v-if="dialogWorkout.description" class="text-sm text-emerald-100 mt-2">
                            {{ dialogWorkout.description }}
                        </DialogDescription>

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

                <div class="px-6 py-4">
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
                    <Button variant="outline" @click="isDialogOpen = false" class="flex-1">
                        Fechar
                    </Button>
                    <Button @click="startWorkout" class="flex-1 gap-2">
                        <Play class="h-4 w-4" />
                        Iniciar Treino
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
