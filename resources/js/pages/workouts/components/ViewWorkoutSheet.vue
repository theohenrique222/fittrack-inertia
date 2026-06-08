<script setup lang="ts">
import { Clock, Dumbbell, Repeat, Trophy, User } from 'lucide-vue-next';

interface WorkoutExercise {
    id: number;
    name: string;
    category?: {
        id: number;
        name: string;
    } | null;
    pivot: {
        sets: number;
        reps: number;
        rest_seconds: number;
        weight: number | null;
        order: number;
        notes?: string;
    };
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    client?: {
        id: number;
        name: string;
        nickname?: string;
    };
    client_id: number;
    trainer?: {
        id: number;
        name: string;
    };
    trainer_id: number;
    exercises?: WorkoutExercise[];
    is_active: boolean;
}

defineProps<{
    workout: Workout;
}>();

function formatRest(seconds: number): string {
    if (seconds >= 60) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;

        return secs > 0 ? `${mins}m${secs}s` : `${mins}min`;
    }

    return `${seconds}s`;
}

const categoryColors: Record<string, string> = {
    Peitoral: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    Costas: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    Ombros: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
    Biceps: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
    Triceps: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
    Antebracos: 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300',
    Abdomen: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
    Quadriceps: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300',
    'Posterior de Coxa': 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
    Gluteos: 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300',
    Panturrilhas: 'bg-lime-100 text-lime-700 dark:bg-lime-900/40 dark:text-lime-300',
    'Corpo Inteiro': 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
};

function getCategoryColor(categoryName?: string): string {
    if (!categoryName) {
return 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300';
}

    return categoryColors[categoryName] || 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300';
}

function getExerciseIcon(index: number): string {
    return String(index + 1).padStart(2, '0');
}
</script>

<template>
    <div v-if="workout" class="flex flex-col h-full">
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white/20"></div>
                <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/10"></div>
            </div>

            <div class="relative">
                <div class="mb-3 flex items-center gap-2">
                    <Dumbbell class="h-5 w-5" />
                    <span class="text-sm font-medium uppercase tracking-wider text-blue-100">Treino</span>
                </div>

                <h2 class="text-2xl font-bold">{{ workout.name }}</h2>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-sm backdrop-blur-sm">
                        <User class="h-3.5 w-3.5" />
                        <span>{{ workout.client?.name || workout.client?.nickname || 'N/A' }}</span>
                    </div>

                    <div v-if="workout.exercises?.length" class="flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1.5 text-sm backdrop-blur-sm">
                        <Trophy class="h-3.5 w-3.5" />
                        <span>{{ workout.exercises.length }} exercícios</span>
                    </div>
                </div>

                <p v-if="workout.description" class="mt-3 text-sm text-blue-100">
                    {{ workout.description }}
                </p>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Exercícios</h3>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                    {{ workout.exercises?.length || 0 }} total
                </span>
            </div>

            <div v-if="workout.exercises?.length" class="space-y-3">
                <div
                    v-for="(exercise, index) in workout.exercises"
                    :key="exercise.id"
                    class="group overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-emerald-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                >
                    <div class="flex items-start gap-4 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 text-sm font-bold text-white shadow-sm">
                            {{ getExerciseIcon(index) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="font-semibold text-neutral-900 dark:text-white">{{ exercise.name }}</h4>
                                <span
                                    v-if="exercise.category"
                                    :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium', getCategoryColor(exercise.category.name)]"
                                >
                                    {{ exercise.category.name }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <div class="flex items-center gap-1.5 rounded-lg bg-neutral-50 px-3 py-2 dark:bg-neutral-700/50">
                                    <Dumbbell class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-bold text-neutral-900 dark:text-white">{{ exercise.pivot.sets }}</span>
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">séries</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 rounded-lg bg-neutral-50 px-3 py-2 dark:bg-neutral-700/50">
                                    <Repeat class="h-4 w-4 text-green-600 dark:text-green-400" />
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-lg font-bold text-neutral-900 dark:text-white">{{ exercise.pivot.reps }}</span>
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">reps</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 rounded-lg bg-neutral-50 px-3 py-2 dark:bg-neutral-700/50">
                                    <Clock class="h-4 w-4 text-orange-600 dark:text-orange-400" />
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ formatRest(exercise.pivot.rest_seconds) }}</span>
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">descanso</span>
                                    </div>
                                </div>

                                <div v-if="exercise.pivot.weight" class="flex items-center gap-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 px-3 py-2">
                                    <span class="text-lg font-bold text-neutral-900 dark:text-white">{{ exercise.pivot.weight }}</span>
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">kg</span>
                                </div>
                            </div>

                            <p v-if="exercise.pivot.notes" class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">
                                {{ exercise.pivot.notes }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                <Dumbbell class="mb-3 h-12 w-12 text-neutral-300 dark:text-neutral-600" />
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Nenhum exercício adicionado</p>
            </div>
        </div>
    </div>
</template>
