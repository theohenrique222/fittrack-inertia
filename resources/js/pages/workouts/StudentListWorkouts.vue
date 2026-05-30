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
import { Head, Link } from '@inertiajs/vue3';
import { ChevronRight, Dumbbell, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
                <Link
                    v-for="workout in filteredWorkouts"
                    :key="workout.id"
                    :href="`/workouts/${workout.id}`"
                    class="group bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden transition-all hover:border-emerald-300 hover:shadow-lg dark:hover:border-emerald-600 block"
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
                </Link>
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
</template>
