<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Meus Treinos',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ChevronRight, Clock, Dumbbell, Play, Repeat, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface ExercisePivot {
    sets: number;
    reps: number;
    rest_seconds: number;
    order: number;
    notes?: string;
}

interface WorkoutExercise {
    id: number;
    name: string;
    category?: {
        id: number;
        name: string;
    } | null;
    pivot: ExercisePivot;
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    is_active: boolean;
    exercises?: WorkoutExercise[];
}

const props = defineProps<{
    workouts: Workout[];
    stats: {
        total: number;
        totalExercises: number;
    };
}>();

const searchQuery = ref('');
const selectedWorkout = ref<Workout | null>(null);
const isDialogOpen = ref(false);

const filteredWorkouts = computed(() => {
    let result = props.workouts;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (w) =>
                w.name.toLowerCase().includes(query) ||
                w.description?.toLowerCase().includes(query),
        );
    }

    return result;
});

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

const avatarColors = [
    'from-emerald-400 to-emerald-600',
    'from-teal-400 to-teal-600',
    'from-cyan-400 to-cyan-600',
    'from-green-400 to-green-600',
    'from-lime-400 to-lime-600',
];

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}

function openWorkoutDialog(workout: Workout) {
    selectedWorkout.value = workout;
    isDialogOpen.value = true;
}

function startWorkout() {
    if (selectedWorkout.value) {
        isDialogOpen.value = false;
        router.visit(`/workouts/${selectedWorkout.value.id}`);
    }
}

function formatRest(seconds: number): string {
    if (seconds >= 60) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return secs > 0 ? `${mins}m${secs}s` : `${mins}min`;
    }
    return `${seconds}s`;
}

function estimatedTime(workout: Workout): string {
    if (!workout.exercises?.length) return '—';
    const totalSeconds = workout.exercises.reduce((sum, ex) => {
        return sum + ex.pivot.sets * (ex.pivot.reps * 3 + ex.pivot.rest_seconds);
    }, 0);
    const mins = Math.round(totalSeconds / 60);
    return `~${mins}min`;
}

function totalReps(workout: Workout): number {
    if (!workout.exercises?.length) return 0;
    return workout.exercises.reduce((sum, ex) => sum + ex.pivot.sets * ex.pivot.reps, 0);
}
</script>

<template>
    <Head title="Meus Treinos" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Meus Treinos</h1>
                <p class="text-sm text-emerald-100 mt-1">Visualize todos os seus treinos</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white/15 backdrop-blur-sm px-4 py-3">
                    <div class="flex items-center gap-2 mb-1">
                        <Dumbbell class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Total</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 backdrop-blur-sm px-4 py-3">
                    <div class="flex items-center gap-2 mb-1">
                        <ChevronRight class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Exercícios</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.totalExercises }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 dark:bg-neutral-900 px-6 py-5">
            <div class="relative mb-5">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
                <input
                    v-model="searchQuery"
                    placeholder="Buscar treino..."
                    class="w-full rounded-lg border border-neutral-200 bg-white pl-9 pr-4 py-2 text-sm text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder-neutral-500"
                />
            </div>

            <div v-if="filteredWorkouts.length" class="space-y-3">
                <div
                    v-for="workout in filteredWorkouts"
                    :key="workout.id"
                    class="group bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden transition-all hover:border-emerald-300 hover:shadow-lg dark:hover:border-emerald-600 cursor-pointer"
                    @click="openWorkoutDialog(workout)"
                >
                    <div class="flex items-center gap-4 p-4">
                        <div
                            :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white font-bold text-sm shadow-sm', getAvatarColor(workout.id)]"
                        >
                            {{ getInitials(workout.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-neutral-900 dark:text-white truncate">{{ workout.name }}</h3>
                                <span
                                    v-if="workout.exercises?.length"
                                    class="shrink-0 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                                >
                                    {{ workout.exercises.length }}
                                </span>
                                <span
                                    v-if="workout.is_active"
                                    class="shrink-0 inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                                >
                                    Ativo
                                </span>
                            </div>
                            <p v-if="workout.description" class="text-sm text-neutral-500 dark:text-neutral-400 mt-1 truncate">
                                {{ workout.description }}
                            </p>
                        </div>

                        <ChevronRight class="h-5 w-5 text-neutral-300 dark:text-neutral-600 group-hover:text-emerald-500 transition-colors shrink-0" />
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800 mb-4">
                    <Dumbbell class="h-8 w-8 text-neutral-300 dark:text-neutral-600" />
                </div>
                <p class="font-medium text-neutral-600 dark:text-neutral-400">
                    {{ searchQuery ? 'Nenhum treino encontrado' : 'Nenhum treino cadastrado' }}
                </p>
                <p class="text-sm text-neutral-400 dark:text-neutral-500 mt-1">
                    {{ searchQuery ? 'Tente buscar com outros termos' : 'Seu personal ainda não cadastrou treinos para você' }}
                </p>
            </div>
        </div>
    </div>

    <Dialog v-model:open="isDialogOpen">
        <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto">
            <DialogHeader class="text-left">
                <div class="flex items-center justify-between gap-2">
                    <DialogTitle class="text-xl font-bold text-neutral-900 dark:text-white truncate">
                        {{ selectedWorkout?.name }}
                    </DialogTitle>
                    <span
                        v-if="selectedWorkout?.is_active"
                        class="shrink-0 inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                    >
                        Ativo
                    </span>
                </div>
                <DialogDescription class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ selectedWorkout?.description || 'Resumo do treino' }}
                </DialogDescription>
            </DialogHeader>

            <!-- Stats Cards -->
            <div v-if="selectedWorkout" class="grid grid-cols-3 gap-3 py-2">
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 text-center">
                    <Dumbbell class="h-5 w-5 mx-auto mb-1.5 text-emerald-500" />
                    <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300">{{ selectedWorkout.exercises?.length || 0 }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400">Exercícios</p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 text-center">
                    <Repeat class="h-5 w-5 mx-auto mb-1.5 text-blue-500" />
                    <p class="text-xl font-bold text-blue-700 dark:text-blue-300">{{ totalReps(selectedWorkout) }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400">Total Reps</p>
                </div>

                <div class="bg-violet-50 dark:bg-violet-900/20 rounded-xl p-3 text-center">
                    <Clock class="h-5 w-5 mx-auto mb-1.5 text-violet-500" />
                    <p class="text-xl font-bold text-violet-700 dark:text-violet-300">{{ estimatedTime(selectedWorkout) }}</p>
                    <p class="text-xs text-violet-600 dark:text-violet-400">Tempo Est.</p>
                </div>
            </div>

            <!-- Exercise List -->
            <div v-if="selectedWorkout?.exercises?.length" class="space-y-1.5">
                <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300 mb-2">Exercícios</h4>
                <div
                    v-for="(exercise, index) in selectedWorkout.exercises"
                    :key="exercise.id"
                    class="flex items-center gap-3 rounded-lg border border-neutral-100 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800/50 px-3 py-2.5"
                >
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-xs font-bold text-emerald-700 dark:text-emerald-300">
                        {{ index + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ exercise.name }}</p>
                        <div class="flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            <span>{{ exercise.pivot.sets }} séries</span>
                            <span class="text-neutral-300 dark:text-neutral-600">·</span>
                            <span>{{ exercise.pivot.reps }} reps</span>
                            <span v-if="exercise.pivot.rest_seconds" class="text-neutral-300 dark:text-neutral-600">·</span>
                            <span v-if="exercise.pivot.rest_seconds">descanso {{ formatRest(exercise.pivot.rest_seconds) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else-if="selectedWorkout" class="flex flex-col items-center justify-center py-8 text-center">
                <Dumbbell class="h-10 w-10 text-neutral-300 dark:text-neutral-600 mb-2" />
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Nenhum exercício neste treino</p>
            </div>

            <DialogFooter class="mt-4 gap-2">
                <button
                    class="flex-1 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-4 py-2.5 text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors"
                    @click="isDialogOpen = false"
                >
                    Fechar
                </button>
                <button
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors"
                    @click="startWorkout"
                >
                    <Play class="h-4 w-4" />
                    Iniciar Treino
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
