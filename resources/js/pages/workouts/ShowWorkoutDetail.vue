<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Check,
    Clock,
    Dumbbell,
    Play,
    ChevronRight,
    Info,
    Film,
    Image as ImageIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';

interface ExerciseCategory {
    id: number;
    name: string;
}

interface ExercisePivot {
    sets: number;
    reps: number;
    rest_seconds: number;
    order: number;
    notes?: string;
}

interface Exercise {
    id: number;
    name: string;
    slug: string;
    description?: string;
    muscle_group?: string;
    equipment?: string;
    difficulty?: string;
    instructions?: string;
    image?: string;
    video_url?: string;
    category?: ExerciseCategory | null;
    pivot: ExercisePivot;
    completed?: boolean;
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    is_active: boolean;
    exercises?: Exercise[];
    client?: {
        id: number;
        name: string;
        nickname?: string;
    };
}

defineProps<{
    workout: Workout;
    exercises: Exercise[];
}>();

const page = usePage();

const isStudent = computed(() => (page.props.auth.user as any)?.role === 'client');

const selectedExercise = ref<Exercise | null>(null);
const isExerciseDetailOpen = ref(false);

const muscleGroupColors: Record<string, { bg: string; text: string; dot: string; border: string; gradient: string }> = {
    Peito: { bg: 'bg-rose-50 dark:bg-rose-900/20', text: 'text-rose-700 dark:text-rose-300', dot: 'bg-rose-500', border: 'border-rose-200 dark:border-rose-800', gradient: 'from-rose-500 to-rose-600' },
    Costas: { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-700 dark:text-blue-300', dot: 'bg-blue-500', border: 'border-blue-200 dark:border-blue-800', gradient: 'from-blue-500 to-blue-600' },
    Pernas: { bg: 'bg-violet-50 dark:bg-violet-900/20', text: 'text-violet-700 dark:text-violet-300', dot: 'bg-violet-500', border: 'border-violet-200 dark:border-violet-800', gradient: 'from-violet-500 to-violet-600' },
    Ombros: { bg: 'bg-orange-50 dark:bg-orange-900/20', text: 'text-orange-700 dark:text-orange-300', dot: 'bg-orange-500', border: 'border-orange-200 dark:border-orange-800', gradient: 'from-orange-500 to-orange-600' },
    Bíceps: { bg: 'bg-pink-50 dark:bg-pink-900/20', text: 'text-pink-700 dark:text-pink-300', dot: 'bg-pink-500', border: 'border-pink-200 dark:border-pink-800', gradient: 'from-pink-500 to-pink-600' },
    Tríceps: { bg: 'bg-indigo-50 dark:bg-indigo-900/20', text: 'text-indigo-700 dark:text-indigo-300', dot: 'bg-indigo-500', border: 'border-indigo-200 dark:border-indigo-800', gradient: 'from-indigo-500 to-indigo-600' },
    Abdômen: { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-700 dark:text-emerald-300', dot: 'bg-emerald-500', border: 'border-emerald-200 dark:border-emerald-800', gradient: 'from-emerald-500 to-emerald-600' },
    Glúteos: { bg: 'bg-amber-50 dark:bg-amber-900/20', text: 'text-amber-700 dark:text-amber-300', dot: 'bg-amber-500', border: 'border-amber-200 dark:border-amber-800', gradient: 'from-amber-500 to-amber-600' },
    Antebraço: { bg: 'bg-teal-50 dark:bg-teal-900/20', text: 'text-teal-700 dark:text-teal-300', dot: 'bg-teal-500', border: 'border-teal-200 dark:border-teal-800', gradient: 'from-teal-500 to-teal-600' },
    Trapézio: { bg: 'bg-cyan-50 dark:bg-cyan-900/20', text: 'text-cyan-700 dark:text-cyan-300', dot: 'bg-cyan-500', border: 'border-cyan-200 dark:border-cyan-800', gradient: 'from-cyan-500 to-cyan-600' },
    Lombar: { bg: 'bg-lime-50 dark:bg-lime-900/20', text: 'text-lime-700 dark:text-lime-300', dot: 'bg-lime-500', border: 'border-lime-200 dark:border-lime-800', gradient: 'from-lime-500 to-lime-600' },
    Panturrilha: { bg: 'bg-fuchsia-50 dark:bg-fuchsia-900/20', text: 'text-fuchsia-700 dark:text-fuchsia-300', dot: 'bg-fuchsia-500', border: 'border-fuchsia-200 dark:border-fuchsia-800', gradient: 'from-fuchsia-500 to-fuchsia-600' },
};

function getMuscleGroupStyle(group?: string) {
    if (!group) return { bg: 'bg-neutral-50 dark:bg-neutral-800/50', text: 'text-neutral-600 dark:text-neutral-400', dot: 'bg-neutral-400', border: 'border-neutral-200 dark:border-neutral-700', gradient: 'from-neutral-400 to-neutral-500' };
    return muscleGroupColors[group] || { bg: 'bg-neutral-50 dark:bg-neutral-800/50', text: 'text-neutral-600 dark:text-neutral-400', dot: 'bg-neutral-400', border: 'border-neutral-200 dark:border-neutral-700', gradient: 'from-neutral-400 to-neutral-500' };
}

function getGroupedExercises(workout: Workout): { group: string; exercises: Exercise[]; style: ReturnType<typeof getMuscleGroupStyle> }[] {
    if (!workout.exercises?.length) return [];
    const groups = new Map<string, Exercise[]>();
    workout.exercises.forEach((ex) => {
        const key = ex.muscle_group || 'Outros';
        if (!groups.has(key)) groups.set(key, []);
        groups.get(key)!.push(ex);
    });
    const order = Object.keys(muscleGroupColors);
    return Array.from(groups.entries())
        .sort((a, b) => {
            const ia = order.indexOf(a[0]);
            const ib = order.indexOf(b[0]);
            if (ia === -1 && ib === -1) return a[0].localeCompare(b[0]);
            if (ia === -1) return 1;
            if (ib === -1) return -1;
            return ia - ib;
        })
        .map(([group, exercises]) => ({
            group,
            exercises,
            style: getMuscleGroupStyle(group),
        }));
}

function workoutTotalSets(workout: Workout): number {
    if (!workout.exercises?.length) return 0;
    return workout.exercises.reduce((sum, ex) => sum + ex.pivot.sets, 0);
}

function workoutTotalReps(workout: Workout): number {
    if (!workout.exercises?.length) return 0;
    return workout.exercises.reduce((sum, ex) => sum + ex.pivot.sets * ex.pivot.reps, 0);
}

function workoutEstimatedTime(workout: Workout): string {
    if (!workout.exercises?.length) return '—';
    const totalSeconds = workout.exercises.reduce((sum, ex) => {
        return sum + ex.pivot.sets * (ex.pivot.reps * 3 + ex.pivot.rest_seconds);
    }, 0);
    const mins = Math.round(totalSeconds / 60);
    return `~${mins}min`;
}

function openExerciseDetail(exercise: Exercise) {
    selectedExercise.value = exercise;
    isExerciseDetailOpen.value = true;
}

function formatRestSeconds(seconds: number): string {
    if (seconds < 60) {
        return `${seconds}s`;
    }

    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    return remainingSeconds > 0 ? `${minutes}m${remainingSeconds}s` : `${minutes}m`;
}

function getDifficultyColor(difficulty?: string): string {
    switch (difficulty?.toLowerCase()) {
        case 'beginner':
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300';
        case 'intermediate':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300';
        case 'advanced':
            return 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
        default:
            return 'bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300';
    }
}

function getDifficultyLabel(difficulty?: string): string {
    switch (difficulty?.toLowerCase()) {
        case 'beginner':
            return 'Iniciante';
        case 'intermediate':
            return 'Intermediário';
        case 'advanced':
            return 'Avançado';
        default:
            return difficulty || '-';
    }
}

function toggleCompletion(exercise: Exercise) {
    router.post(
        `/workouts/${(page.props as any).workout.id}/exercises/${exercise.id}/toggle-completion`,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

const completedExercises = computed(() => {
    return ((page.props as any).workout as Workout)?.exercises?.filter((e) => e.completed) ?? [];
});
</script>

<template>
    <Head :title="workout.name" />

    <div class="min-h-screen bg-neutral-50 dark:bg-neutral-900">
        <!-- Header -->
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-lg border-b border-neutral-200 dark:border-neutral-700 dark:bg-neutral-800/80">
            <div class="max-w-3xl mx-auto px-4 py-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="isStudent ? '/dashboard' : `/students/${workout.client?.id}`"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700 hover:bg-neutral-200 dark:hover:bg-neutral-600 transition-colors"
                    >
                        <ArrowLeft class="h-5 w-5 text-neutral-600 dark:text-neutral-300" />
                    </Link>

                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-bold text-neutral-900 dark:text-white truncate">
                            {{ workout.name }}
                        </h1>
                        <p v-if="workout.client" class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ workout.client.name }}{{ workout.client.nickname ? ` (${workout.client.nickname})` : '' }}
                        </p>
                    </div>

                    <Badge :class="workout.is_active ? 'bg-emerald-500 text-white' : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300'">
                        {{ workout.is_active ? 'Ativo' : 'Inativo' }}
                    </Badge>
                </div>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-4 py-6">
            <!-- Description -->
            <div v-if="workout.description" class="mb-6 p-4 rounded-xl bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700">
                <p class="text-sm text-neutral-700 dark:text-neutral-300 leading-relaxed">
                    {{ workout.description }}
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-4 gap-3 mb-6">
                <div class="bg-white dark:bg-neutral-800 rounded-xl p-4 border border-neutral-200 dark:border-neutral-700 text-center">
                    <Dumbbell class="h-5 w-5 mx-auto mb-2 text-emerald-500" />
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workout.exercises?.length || 0 }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Exercícios</p>
                </div>

                <div class="bg-white dark:bg-neutral-800 rounded-xl p-4 border border-neutral-200 dark:border-neutral-700 text-center">
                    <Play class="h-5 w-5 mx-auto mb-2 text-blue-500" />
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutTotalSets(workout) }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Séries</p>
                </div>

                <div class="bg-white dark:bg-neutral-800 rounded-xl p-4 border border-neutral-200 dark:border-neutral-700 text-center">
                    <Clock class="h-5 w-5 mx-auto mb-2 text-violet-500" />
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutTotalReps(workout) }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Reps</p>
                </div>

                <div class="bg-white dark:bg-neutral-800 rounded-xl p-4 border border-neutral-200 dark:border-neutral-700 text-center">
                    <Clock class="h-5 w-5 mx-auto mb-2 text-amber-500" />
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutEstimatedTime(workout) }}</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Tempo Est.</p>
                </div>
            </div>

            <!-- Muscle Groups Summary -->
            <div v-if="getGroupedExercises(workout).length > 1" class="mb-6">
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="({ group, exercises, style }) in getGroupedExercises(workout)"
                        :key="group"
                        :class="['inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium border', style.bg, style.text, style.border]"
                    >
                        <span :class="['h-2 w-2 rounded-full', style.dot]"></span>
                        {{ group }}
                        <span class="opacity-60">({{ exercises.length }})</span>
                    </span>
                </div>
            </div>

            <!-- Exercises Grouped by Muscle Group -->
            <div class="space-y-6">
                <div class="flex items-center gap-2">
                    <Dumbbell class="h-5 w-5 text-emerald-500" />
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Exercícios</h2>
                </div>

                <template v-if="getGroupedExercises(workout).length">
                    <div
                        v-for="({ group, exercises, style }) in getGroupedExercises(workout)"
                        :key="group"
                        class="space-y-2"
                    >
                        <!-- Muscle Group Section Header -->
                        <div :class="['flex items-center gap-3 px-4 py-2.5 rounded-xl border', style.bg, style.border]">
                            <span :class="['flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br text-white text-sm font-bold shadow-sm', style.gradient]">
                                {{ exercises.length }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <h3 :class="['font-semibold text-sm', style.text]">{{ group }}</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ exercises.length }} {{ exercises.length === 1 ? 'exercício' : 'exercícios' }}
                                    · {{ exercises.reduce((s, e) => s + e.pivot.sets, 0) }} séries
                                    · {{ exercises.reduce((s, e) => s + e.pivot.sets * e.pivot.reps, 0) }} reps
                                </p>
                            </div>
                        </div>

                        <!-- Exercises in this group -->
                        <div class="space-y-2 ml-2">
                            <div
                                v-for="(exercise, index) in exercises"
                                :key="exercise.id"
                                :class="[
                                    'group bg-white dark:bg-neutral-800 rounded-xl border overflow-hidden hover:shadow-lg transition-all cursor-pointer',
                                    exercise.completed
                                        ? 'border-emerald-400 dark:border-emerald-600 bg-emerald-50/50 dark:bg-emerald-900/10'
                                        : 'border-neutral-200 dark:border-neutral-700 hover:border-emerald-300 dark:hover:border-emerald-600',
                                ]"
                                @click="openExerciseDetail(exercise)"
                            >
                                <div class="p-4">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="mt-1 shrink-0"
                                            @click.stop="toggleCompletion(exercise)"
                                        >
                                            <Checkbox :checked="exercise.completed" />
                                        </div>
                                        <div :class="[
                                            'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-base font-bold',
                                            exercise.completed
                                                ? 'bg-emerald-500 text-white'
                                                : 'bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/30 dark:to-teal-900/30 text-emerald-700 dark:text-emerald-300',
                                        ]">
                                            <Check v-if="exercise.completed" class="h-5 w-5" />
                                            <span v-else>{{ index + 1 }}</span>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="font-semibold text-neutral-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                        {{ exercise.name }}
                                                    </h3>

                                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                                        <span v-if="exercise.category" class="text-xs text-neutral-500 dark:text-neutral-400">
                                                            {{ exercise.category.name }}
                                                        </span>

                                                        <Badge v-if="exercise.difficulty" :class="getDifficultyColor(exercise.difficulty)" class="text-xs px-2 py-0">
                                                            {{ getDifficultyLabel(exercise.difficulty) }}
                                                        </Badge>

                                                        <span v-if="exercise.muscle_group" class="text-xs text-neutral-400 dark:text-neutral-500">
                                                            {{ exercise.muscle_group }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <ChevronRight class="h-5 w-5 text-neutral-300 dark:text-neutral-600 group-hover:text-emerald-500 transition-colors shrink-0 mt-1" />
                                            </div>

                                            <div class="mt-3 grid grid-cols-3 gap-3">
                                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-lg p-2 text-center">
                                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Séries</p>
                                                    <p class="text-lg font-bold text-neutral-900 dark:text-white">{{ exercise.pivot.sets }}</p>
                                                </div>

                                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-lg p-2 text-center">
                                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Reps</p>
                                                    <p class="text-lg font-bold text-neutral-900 dark:text-white">{{ exercise.pivot.reps }}</p>
                                                </div>

                                                <div class="bg-neutral-50 dark:bg-neutral-900/50 rounded-lg p-2 text-center">
                                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Descanso</p>
                                                    <p class="text-lg font-bold text-neutral-900 dark:text-white">{{ formatRestSeconds(exercise.pivot.rest_seconds) }}</p>
                                                </div>
                                            </div>

                                            <p v-if="exercise.pivot.notes" class="mt-2 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                                <Info class="h-3 w-3" />
                                                {{ exercise.pivot.notes }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div v-else class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800 mb-4">
                        <Dumbbell class="h-8 w-8 text-neutral-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nenhum exercício</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">
                        Este treino ainda não possui exercícios
                    </p>
                </div>
            </div>

            <!-- Completed Exercises -->
            <div v-if="completedExercises.length" class="mt-8">
                <div class="flex items-center gap-2 mb-4">
                    <Check class="h-5 w-5 text-emerald-500" />
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Exercícios Concluídos
                    </h2>
                    <span class="text-sm text-neutral-500 dark:text-neutral-400">
                        ({{ completedExercises.length }} de {{ workout.exercises?.length || 0 }})
                    </span>
                </div>

                <div class="bg-white dark:bg-neutral-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
                    <div
                        v-for="(exercise, index) in completedExercises"
                        :key="exercise.id"
                        class="flex items-center gap-3 px-4 py-3 border-b border-emerald-100 dark:border-emerald-900/30 last:border-b-0"
                    >
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                            <Check class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">
                                {{ exercise.name }}
                            </p>
                            <p v-if="exercise.muscle_group" class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ exercise.muscle_group }}
                            </p>
                        </div>
                        <span class="text-xs text-neutral-400 dark:text-neutral-500">
                            {{ exercise.pivot.sets }}×{{ exercise.pivot.reps }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exercise Detail Sheet -->
    <Sheet v-model:open="isExerciseDetailOpen">
        <SheetContent side="right" class="w-full sm:max-w-lg p-0">
            <SheetHeader class="sr-only">
                <SheetTitle>{{ selectedExercise?.name }}</SheetTitle>
            </SheetHeader>

            <div v-if="selectedExercise" class="flex flex-col h-full">
                <!-- Hero Image/Video -->
                <div v-if="selectedExercise.image || selectedExercise.video_url" class="relative bg-neutral-900 aspect-video">
                    <img
                        v-if="selectedExercise.image"
                        :src="selectedExercise.image"
                        :alt="selectedExercise.name"
                        class="w-full h-full object-cover"
                    />

                    <div v-if="selectedExercise.video_url" class="absolute inset-0 flex items-center justify-center bg-black/40">
                        <a
                            :href="selectedExercise.video_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2 px-4 py-2 bg-white/90 rounded-full text-sm font-medium text-neutral-900 hover:bg-white transition-colors"
                        >
                            <Film class="h-4 w-4" />
                            Assistir vídeo
                        </a>
                    </div>

                    <div v-else-if="selectedExercise.image" class="absolute top-3 right-3">
                        <Badge class="bg-white/90 text-neutral-900">
                            <ImageIcon class="h-3 w-3 mr-1" />
                            Foto disponível
                        </Badge>
                    </div>
                </div>

                <div v-else class="bg-gradient-to-br from-emerald-500 to-teal-500 aspect-video flex items-center justify-center">
                    <Dumbbell class="h-20 w-20 text-white/50" />
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
                    <!-- Title & Badges -->
                    <div>
                        <h2 class="text-xl font-bold text-neutral-900 dark:text-white">
                            {{ selectedExercise.name }}
                        </h2>

                        <div class="flex flex-wrap gap-2 mt-2">
                            <Badge v-if="selectedExercise.category" variant="secondary" class="text-xs">
                                {{ selectedExercise.category.name }}
                            </Badge>

                            <Badge v-if="selectedExercise.difficulty" :class="getDifficultyColor(selectedExercise.difficulty)" class="text-xs">
                                {{ getDifficultyLabel(selectedExercise.difficulty) }}
                            </Badge>

                            <Badge v-if="selectedExercise.muscle_group" variant="outline" class="text-xs">
                                {{ selectedExercise.muscle_group }}
                            </Badge>

                            <Badge v-if="selectedExercise.equipment" variant="outline" class="text-xs">
                                {{ selectedExercise.equipment }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Workout Config -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 text-center">
                            <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Séries</p>
                            <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ selectedExercise.pivot.sets }}</p>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 text-center">
                            <p class="text-xs font-medium text-blue-600 dark:text-blue-400">Repetições</p>
                            <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ selectedExercise.pivot.reps }}</p>
                        </div>

                        <div class="bg-violet-50 dark:bg-violet-900/20 rounded-xl p-3 text-center">
                            <p class="text-xs font-medium text-violet-600 dark:text-violet-400">Descanso</p>
                            <p class="text-2xl font-bold text-violet-700 dark:text-violet-300">{{ formatRestSeconds(selectedExercise.pivot.rest_seconds) }}</p>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="selectedExercise.description">
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-2">Descrição</h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            {{ selectedExercise.description }}
                        </p>
                    </div>

                    <!-- Instructions -->
                    <div v-if="selectedExercise.instructions">
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-2">Instruções</h3>
                        <div class="space-y-2">
                            <p
                                v-for="(line, i) in selectedExercise.instructions.split('\n').filter(l => l.trim())"
                                :key="i"
                                class="text-sm text-neutral-600 dark:text-neutral-400 flex items-start gap-2"
                            >
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">
                                    {{ i + 1 }}
                                </span>
                                {{ line.trim() }}
                            </p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="selectedExercise.pivot.notes">
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-2">Observações do Treino</h3>
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-sm text-amber-800 dark:text-amber-300">
                            {{ selectedExercise.pivot.notes }}
                        </div>
                    </div>

                    <!-- Video Link -->
                    <div v-if="selectedExercise.video_url">
                        <a
                            :href="selectedExercise.video_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-3 p-3 rounded-lg bg-neutral-50 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors group"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 group-hover:bg-red-200 dark:group-hover:bg-red-900/50 transition-colors">
                                <Film class="h-5 w-5" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">Ver vídeo demonstrativo</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ selectedExercise.video_url }}</p>
                            </div>
                            <ChevronRight class="h-5 w-5 text-neutral-400 group-hover:text-emerald-500 transition-colors" />
                        </a>
                    </div>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
