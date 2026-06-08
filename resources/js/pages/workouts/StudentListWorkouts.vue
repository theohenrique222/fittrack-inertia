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
import { Check, ChevronRight, Clock, Dumbbell, Play, Repeat, Search, Target, History, TrendingUp } from 'lucide-vue-next';
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
    weight: number | null;
    order: number;
    notes?: string;
}

interface WorkoutExercise {
    id: number;
    name: string;
    muscle_group?: string;
    category?: {
        id: number;
        name: string;
    } | null;
    pivot: ExercisePivot;
    completed?: boolean;
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    is_active: boolean;
    exercises?: WorkoutExercise[];
    total_sessions?: number;
    last_completed_at?: string;
    avg_duration_minutes?: number | null;
}

interface SessionHistoryItem {
    id: number;
    workout_id: number;
    workout_name: string;
    completed_at: string;
    completed_at_full: string;
    duration_minutes: number | null;
}

const props = defineProps<{
    workouts: Workout[];
    sessionHistory: SessionHistoryItem[];
    stats: {
        total: number;
        totalExercises: number;
        totalCompleted: number;
    };
    customWeights?: Record<number, Record<number, number | null>>;
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

const muscleGroupColors: Record<string, { bg: string; text: string; dot: string; section: string }> = {
    Peito: { bg: 'bg-rose-100 dark:bg-rose-900/30', text: 'text-rose-700 dark:text-rose-300', dot: 'bg-rose-500', section: 'border-l-rose-400' },
    Costas: { bg: 'bg-blue-100 dark:bg-blue-900/30', text: 'text-blue-700 dark:text-blue-300', dot: 'bg-blue-500', section: 'border-l-blue-400' },
    Pernas: { bg: 'bg-violet-100 dark:bg-violet-900/30', text: 'text-violet-700 dark:text-violet-300', dot: 'bg-violet-500', section: 'border-l-violet-400' },
    Ombros: { bg: 'bg-orange-100 dark:bg-orange-900/30', text: 'text-orange-700 dark:text-orange-300', dot: 'bg-orange-500', section: 'border-l-orange-400' },
    Bíceps: { bg: 'bg-pink-100 dark:bg-pink-900/30', text: 'text-pink-700 dark:text-pink-300', dot: 'bg-pink-500', section: 'border-l-pink-400' },
    Tríceps: { bg: 'bg-indigo-100 dark:bg-indigo-900/30', text: 'text-indigo-700 dark:text-indigo-300', dot: 'bg-indigo-500', section: 'border-l-indigo-400' },
    Abdômen: { bg: 'bg-emerald-100 dark:bg-emerald-900/30', text: 'text-emerald-700 dark:text-emerald-300', dot: 'bg-emerald-500', section: 'border-l-emerald-400' },
    Glúteos: { bg: 'bg-amber-100 dark:bg-amber-900/30', text: 'text-amber-700 dark:text-amber-300', dot: 'bg-amber-500', section: 'border-l-amber-400' },
    Antebraço: { bg: 'bg-teal-100 dark:bg-teal-900/30', text: 'text-teal-700 dark:text-teal-300', dot: 'bg-teal-500', section: 'border-l-teal-400' },
    Trapézio: { bg: 'bg-cyan-100 dark:bg-cyan-900/30', text: 'text-cyan-700 dark:text-cyan-300', dot: 'bg-cyan-500', section: 'border-l-cyan-400' },
    Lombar: { bg: 'bg-lime-100 dark:bg-lime-900/30', text: 'text-lime-700 dark:text-lime-300', dot: 'bg-lime-500', section: 'border-l-lime-400' },
    Panturrilha: { bg: 'bg-fuchsia-100 dark:bg-fuchsia-900/30', text: 'text-fuchsia-700 dark:text-fuchsia-300', dot: 'bg-fuchsia-500', section: 'border-l-fuchsia-400' },
};

function getMuscleGroupStyle(group?: string) {
    if (!group) {
return { bg: 'bg-neutral-100 dark:bg-neutral-800', text: 'text-neutral-600 dark:text-neutral-400', dot: 'bg-neutral-400', section: 'border-l-neutral-300 dark:border-l-neutral-600' };
}

    return muscleGroupColors[group] || { bg: 'bg-neutral-100 dark:bg-neutral-800', text: 'text-neutral-600 dark:text-neutral-400', dot: 'bg-neutral-400', section: 'border-l-neutral-300 dark:border-l-neutral-600' };
}

function workoutMuscleGroups(workout: Workout): string[] {
    if (!workout.exercises?.length) {
return [];
}

    const groups = new Set<string>();
    workout.exercises.forEach((ex) => {
        if (ex.muscle_group) {
groups.add(ex.muscle_group);
}
    });

    return Array.from(groups);
}

function groupedExercises(workout: Workout): { group: string; exercises: WorkoutExercise[] }[] {
    if (!workout.exercises?.length) {
return [];
}

    const groups = new Map<string, WorkoutExercise[]>();
    workout.exercises.forEach((ex) => {
        const key = ex.muscle_group || 'Outros';

        if (!groups.has(key)) {
groups.set(key, []);
}

        groups.get(key)!.push(ex);
    });
    const order = Object.keys(muscleGroupColors);

    return Array.from(groups.entries()).sort((a, b) => {
        const ia = order.indexOf(a[0]);
        const ib = order.indexOf(b[0]);

        if (ia === -1 && ib === -1) {
return a[0].localeCompare(b[0]);
}

        if (ia === -1) {
return 1;
}

        if (ib === -1) {
return -1;
}

        return ia - ib;
    }).map(([group, exercises]) => ({ group, exercises }));
}

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

function effectiveWeight(workoutId: number, exerciseId: number, pivotWeight: number | null): number | null {
    const custom = props.customWeights?.[workoutId]?.[exerciseId];

    if (custom !== undefined && custom !== null) {
        return custom;
    }

    return pivotWeight ?? null;
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
    if (!workout.exercises?.length) {
return '—';
}

    const totalSeconds = workout.exercises.reduce((sum, ex) => {
        return sum + ex.pivot.sets * (ex.pivot.reps * 3 + ex.pivot.rest_seconds);
    }, 0);
    const mins = Math.round(totalSeconds / 60);

    return `~${mins}min`;
}

function totalReps(workout: Workout): number {
    if (!workout.exercises?.length) {
return 0;
}

    return workout.exercises.reduce((sum, ex) => sum + ex.pivot.sets * ex.pivot.reps, 0);
}

function formatDuration(minutes: number | null | undefined): string {
    if (!minutes) {
return '—';
}

    if (minutes < 60) {
        return `${minutes} min`;
    }

    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;

    return mins > 0 ? `${hours}h${mins}min` : `${hours}h`;
}

function formatLastCompleted(date: string | undefined | null): string {
    if (!date) {
return null;
}

    const d = new Date(date);

    return d.toLocaleDateString('pt-BR');
}

</script>

<template>
    <Head title="Meus Treinos" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white">
            <div class="absolute -top-6 -right-6 h-40 w-40 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-emerald-400/10 blur-2xl"></div>

            <div class="relative mb-6">
                <h1 class="text-2xl font-bold">Meus Treinos</h1>
                <p class="text-sm text-emerald-100 mt-1">Visualize todos os seus treinos</p>
            </div>

            <div class="relative grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-white/15 backdrop-blur-sm px-4 py-3 transition hover:bg-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <Dumbbell class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Total de Treinos</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 backdrop-blur-sm px-4 py-3 transition hover:bg-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <Target class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Total de Exercícios</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.totalExercises }}</p>
                </div>

                <div class="rounded-xl bg-white/15 backdrop-blur-sm px-4 py-3 transition hover:bg-white/20">
                    <div class="flex items-center gap-2 mb-1">
                        <TrendingUp class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Treinos Concluídos</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.totalCompleted }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 dark:bg-neutral-900 px-6 py-5">
            <div class="relative mb-5">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
                <input
                    v-model="searchQuery"
                    placeholder="Buscar treino..."
                    class="w-full rounded-lg border border-neutral-200 bg-white pl-9 pr-4 py-2.5 text-sm text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder-neutral-500"
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
                                    {{ workout.exercises.length }} ex
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

                            <!-- Session Metrics -->
                            <div
                                v-if="workout.total_sessions && workout.total_sessions > 0"
                                class="flex flex-wrap items-center gap-3 mt-2"
                            >
                                <span class="inline-flex items-center gap-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    <Repeat class="h-3.5 w-3.5" />
                                    Executado: <strong class="text-neutral-700 dark:text-neutral-300">{{ workout.total_sessions }}x</strong>
                                </span>
                                <span
                                    v-if="workout.last_completed_at"
                                    class="inline-flex items-center gap-1 text-xs text-neutral-500 dark:text-neutral-400"
                                >
                                    <Clock class="h-3.5 w-3.5" />
                                    Última: <strong class="text-neutral-700 dark:text-neutral-300">{{ formatLastCompleted(workout.last_completed_at) }}</strong>
                                </span>
                                <span
                                    v-if="workout.avg_duration_minutes"
                                    class="inline-flex items-center gap-1 text-xs text-neutral-500 dark:text-neutral-400"
                                >
                                    <TrendingUp class="h-3.5 w-3.5" />
                                    Média: <strong class="text-neutral-700 dark:text-neutral-300">{{ formatDuration(workout.avg_duration_minutes) }}</strong>
                                </span>
                            </div>

                            <div v-if="workoutMuscleGroups(workout).length" class="flex flex-wrap gap-1 mt-2">
                                <span
                                    v-for="group in workoutMuscleGroups(workout)"
                                    :key="group"
                                    :class="['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium', getMuscleGroupStyle(group).bg, getMuscleGroupStyle(group).text]"
                                >
                                    <span :class="['h-1.5 w-1.5 rounded-full', getMuscleGroupStyle(group).dot]"></span>
                                    {{ group }}
                                </span>
                            </div>
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

            <!-- Session History Section -->
            <div v-if="sessionHistory.length" class="mt-8">
                <div class="flex items-center gap-2 mb-4">
                    <History class="h-5 w-5 text-emerald-500" />
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Histórico de Treinos</h2>
                    <span class="text-sm text-neutral-500 dark:text-neutral-400">({{ sessionHistory.length }})</span>
                </div>

                <div class="space-y-2">
                    <div
                        v-for="session in sessionHistory"
                        :key="session.id"
                        class="flex items-center gap-3 bg-white dark:bg-neutral-800 rounded-lg border border-neutral-200 dark:border-neutral-700 px-4 py-3"
                    >
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                            <Check class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">
                                {{ session.workout_name }}
                            </p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                Concluído em {{ session.completed_at }}
                            </p>
                        </div>
                        <span
                            v-if="session.duration_minutes"
                            class="shrink-0 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            {{ formatDuration(session.duration_minutes) }}
                        </span>
                    </div>
                </div>
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

            <!-- Session metrics in dialog -->
            <div
                v-if="selectedWorkout?.total_sessions && selectedWorkout.total_sessions > 0"
                class="grid grid-cols-3 gap-3 pb-2"
            >
                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 text-center">
                    <Repeat class="h-5 w-5 mx-auto mb-1.5 text-amber-500" />
                    <p class="text-xl font-bold text-amber-700 dark:text-amber-300">{{ selectedWorkout.total_sessions }}x</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400">Execuções</p>
                </div>

                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-3 text-center">
                    <Clock class="h-5 w-5 mx-auto mb-1.5 text-teal-500" />
                    <p class="text-xl font-bold text-teal-700 dark:text-teal-300">{{ selectedWorkout.last_completed_at ? formatLastCompleted(selectedWorkout.last_completed_at) : '—' }}</p>
                    <p class="text-xs text-teal-600 dark:text-teal-400">Última</p>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-3 text-center">
                    <TrendingUp class="h-5 w-5 mx-auto mb-1.5 text-indigo-500" />
                    <p class="text-xl font-bold text-indigo-700 dark:text-indigo-300">{{ formatDuration(selectedWorkout.avg_duration_minutes) }}</p>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400">Média</p>
                </div>
            </div>

            <div v-if="selectedWorkout?.exercises?.length" class="space-y-4">
                <h4 class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Exercícios</h4>

                <div v-for="({ group, exercises }, gi) in groupedExercises(selectedWorkout)" :key="gi" class="space-y-1.5">
                    <div class="flex items-center gap-2 px-1">
                        <span :class="['h-2 w-2 rounded-full', getMuscleGroupStyle(group).dot]"></span>
                        <span :class="['text-xs font-semibold uppercase tracking-wider', getMuscleGroupStyle(group).text]">
                            {{ group }}
                        </span>
                        <span class="text-xs text-neutral-400 dark:text-neutral-500">({{ exercises.length }})</span>
                    </div>

                    <div
                        v-for="(exercise, index) in exercises"
                        :key="exercise.id"
                        :class="[
                            'flex items-center gap-3 rounded-lg border px-3 py-2.5 ml-3',
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
                                <span>{{ exercise.pivot.sets }} séries</span>
                                <span class="text-neutral-300 dark:text-neutral-600">·</span>
                                <span>{{ exercise.pivot.reps }} reps</span>
                                <span v-if="exercise.pivot.rest_seconds" class="text-neutral-300 dark:text-neutral-600">·</span>
                                <span v-if="exercise.pivot.rest_seconds">descanso {{ formatRest(exercise.pivot.rest_seconds) }}</span>
                                <span v-if="effectiveWeight(selectedWorkout?.id ?? 0, exercise.id, exercise.pivot.weight)" class="text-neutral-300 dark:text-neutral-600">·</span>
                                <span v-if="effectiveWeight(selectedWorkout?.id ?? 0, exercise.id, exercise.pivot.weight)" class="font-medium text-amber-600 dark:text-amber-400">
                                    {{ effectiveWeight(selectedWorkout?.id ?? 0, exercise.id, exercise.pivot.weight) }} kg
                                </span>
                            </div>
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
