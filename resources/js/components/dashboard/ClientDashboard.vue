<script setup lang="ts">
import { computed } from 'vue';
import { Dumbbell, Flame, Trophy, Target, CheckCircle, User, Calendar, Clock } from 'lucide-vue-next';
import BarChart from '@/components/dashboard/BarChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import ProgressRing from '@/components/dashboard/ProgressRing.vue';

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
    upcomingWorkouts: { name: string; date: string; time: string; exercises: number; status: string }[];
    recentAchievements: { title: string; description: string; icon: string; date: string }[];
    trainer: { name: string; specialty: string; email: string };
}

const props = defineProps<Props>();

const iconMap: Record<string, any> = {
    'flame': Flame,
    'trophy': Trophy,
    'target': Target,
    'check-circle': CheckCircle,
};

const completionRate = computed(() => {
    if (props.stats.totalWorkouts === 0) return 0;
    return Math.round((props.stats.completedWorkouts / props.stats.totalWorkouts) * 100);
});

const progressChartData = computed(() => {
    return props.progressData.map((d) => ({
        month: d.week,
        clients: d.weight,
        workouts: d.bodyFat * 5,
    }));
});
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Welcome Header -->
        <div class="rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-500 p-6 text-white shadow-lg">
            <h1 class="text-2xl font-bold">
                Meu Progresso
            </h1>
            <p class="text-sm opacity-90 mt-1">
                Continue assim! Você está no caminho certo 💪
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Treinos Total</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.totalWorkouts }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <Dumbbell class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <ProgressRing :percentage="completionRate" :size="32" :stroke-width="4" color="#10b981" />
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ completionRate }}% concluídos</span>
                </div>
            </div>

            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Sequência Atual</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.currentStreak }} dias</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                        <Flame class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">
                    Recorde pessoal!
                </p>
            </div>

            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Concluídos</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.completedWorkouts }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <CheckCircle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">
                    {{ stats.totalWorkouts - stats.completedWorkouts }} restantes
                </p>
            </div>

            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Exercícios</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.totalExercises }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <Target class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">
                    Disponíveis
                </p>
            </div>
        </div>

        <!-- Body Metrics -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in bodyMetrics"
                :key="metric.label"
                class="rounded-xl border bg-white dark:bg-neutral-900 p-4 shadow-sm"
            >
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ metric.label }}</p>
                <p class="text-xl font-bold mt-1">{{ metric.value }}</p>
                <p
                    class="text-xs mt-1"
                    :class="metric.trend === 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                >
                    {{ metric.change }}
                </p>
            </div>
        </div>

        <!-- Weekly Workouts -->
        <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4">Treinos da Semana</h3>
            <div class="grid grid-cols-7 gap-2">
                <div
                    v-for="workout in weeklyWorkouts"
                    :key="workout.day"
                    class="flex flex-col items-center gap-2 p-3 rounded-lg"
                    :class="workout.completed
                        ? 'bg-emerald-100 dark:bg-emerald-900/30'
                        : workout.type === 'Descanso'
                            ? 'bg-neutral-100 dark:bg-neutral-800'
                            : 'bg-orange-100 dark:bg-orange-900/20'"
                >
                    <span class="text-xs font-medium">{{ workout.day }}</span>
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center"
                        :class="workout.completed
                            ? 'bg-emerald-500 text-white'
                            : workout.type === 'Descanso'
                                ? 'bg-neutral-300 dark:bg-neutral-600 text-neutral-600 dark:text-neutral-300'
                                : 'bg-orange-500 text-white'"
                    >
                        <CheckCircle v-if="workout.completed" class="w-4 h-4" />
                        <span v-else class="text-xs">{{ workout.type.charAt(0) }}</span>
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
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Nutrição do Dia</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <ProgressRing
                            :percentage="nutritionData.calories.percentage"
                            :size="60"
                            :stroke-width="5"
                            color="#10b981"
                        >
                            <span class="text-xs font-bold">{{ nutritionData.calories.consumed }}</span>
                        </ProgressRing>
                        <div>
                            <p class="text-sm font-medium">Calorias</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ nutritionData.calories.consumed }} / {{ nutritionData.calories.target }} kcal
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="flex flex-col items-center gap-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <ProgressRing
                                :percentage="nutritionData.protein.percentage"
                                :size="50"
                                :stroke-width="4"
                                color="#3b82f6"
                            >
                                <span class="text-xs font-bold">{{ nutritionData.protein.consumed }}</span>
                            </ProgressRing>
                            <span class="text-xs">Proteína</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <ProgressRing
                                :percentage="nutritionData.carbs.percentage"
                                :size="50"
                                :stroke-width="4"
                                color="#f59e0b"
                            >
                                <span class="text-xs font-bold">{{ nutritionData.carbs.consumed }}</span>
                            </ProgressRing>
                            <span class="text-xs">Carbos</span>
                        </div>
                        <div class="flex flex-col items-center gap-2 p-3 rounded-lg bg-rose-50 dark:bg-rose-900/20">
                            <ProgressRing
                                :percentage="nutritionData.fat.percentage"
                                :size="50"
                                :stroke-width="4"
                                color="#f43f5e"
                            >
                                <span class="text-xs font-bold">{{ nutritionData.fat.consumed }}</span>
                            </ProgressRing>
                            <span class="text-xs">Gordura</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Evolução de Peso</h3>
                <LineChart :data="progressChartData" :height="200" />
            </div>
        </div>

        <!-- Upcoming Workouts & Achievements -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Upcoming Workouts -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Próximos Treinos</h3>
                <div class="space-y-3">
                    <div
                        v-for="(workout, index) in upcomingWorkouts"
                        :key="index"
                        class="flex items-center justify-between py-3 border-b last:border-b-0"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white">
                                <Dumbbell class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ workout.name }}</p>
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
                    </div>
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Conquistas Recentes</h3>
                <div class="space-y-3">
                    <div
                        v-for="(achievement, index) in recentAchievements"
                        :key="index"
                        class="flex items-start gap-3 py-2 border-b last:border-b-0"
                    >
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white flex-shrink-0">
                            <component :is="iconMap[achievement.icon]" class="w-5 h-5" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ achievement.title }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ achievement.description }}</p>
                            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">{{ achievement.date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trainer Info -->
        <div class="rounded-xl border bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 p-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xl font-bold">
                    {{ trainer.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Seu Personal Trainer</p>
                    <p class="text-lg font-bold">{{ trainer.name }}</p>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ trainer.specialty }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
