<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Alunos',
                href: '/students',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Dumbbell, Mail, Plus } from 'lucide-vue-next';
import { workouts } from '@/routes';

interface Student {
    id: number;
    name: string;
    email: string;
    nickname?: string;
}

interface Exercise {
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
        order: number;
        notes?: string;
    };
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    is_active: boolean;
    exercises?: Exercise[];
    created_at?: string;
}

const props = defineProps<{
    title: string;
    student: Student;
    workout: Workout | null;
}>();

function formatRestSeconds(seconds: number): string {
    if (seconds < 60) {
        return `${seconds}s`;
    }

    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    return remainingSeconds > 0 ? `${minutes}m ${remainingSeconds}s` : `${minutes}m`;
}
</script>

<template>
    <Head :title="title" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <div class="flex items-center gap-4">
            <Link href="/students" class="flex items-center gap-2 text-neutral-500 hover:text-neutral-700 dark:hover:text-neutral-300">
                <ArrowLeft class="w-4 h-4" />
                <span>Voltar</span>
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex flex-col items-center text-center">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-2xl font-bold text-white shadow-lg">
                            {{ student.name ? student.name.charAt(0).toUpperCase() : '?' }}
                        </div>
                        <h2 class="text-xl font-bold text-neutral-900 dark:text-white">{{ student.name }}</h2>
                        <p v-if="student.nickname" class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ student.nickname }}
                        </p>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-center gap-3 text-sm">
                            <Mail class="w-4 h-4 text-neutral-400" />
                            <span class="text-neutral-600 dark:text-neutral-300">{{ student.email }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-neutral-200 dark:border-neutral-700">
                        <Link
                            v-if="student?.id"
                            :href="workouts.url({ query: { client_id: student.id } })"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600 transition-colors"
                        >
                            <Dumbbell class="w-4 h-4" />
                            <span>Ver Treinos</span>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div v-if="workout" class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600">
                                    <Dumbbell class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-900 dark:text-white">{{ workout.name }}</h3>
                                    <p v-if="workout.description" class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ workout.description }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                Ativo
                            </span>
                            <Link
                                :href="workouts.url({ query: { client_id: student.id } })"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-600 transition-colors"
                            >
                                <Dumbbell class="w-3.5 h-3.5" />
                                <span>Ver Treino</span>
                            </Link>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="workout.exercises && workout.exercises.length > 0" class="space-y-4">
                            <div
                                v-for="(exercise, index) in workout.exercises"
                                :key="exercise.id"
                                class="rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-900/50"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-sm font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-neutral-900 dark:text-white">{{ exercise.name }}</h4>
                                            <p v-if="exercise.category" class="text-xs text-neutral-500 dark:text-neutral-400">
                                                {{ exercise.category.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-3 gap-4">
                                    <div class="text-center">
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Séries</p>
                                        <p class="text-lg font-semibold text-neutral-900 dark:text-white">{{ exercise.pivot.sets }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Repetições</p>
                                        <p class="text-lg font-semibold text-neutral-900 dark:text-white">{{ exercise.pivot.reps }}</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Descanso</p>
                                        <p class="text-lg font-semibold text-neutral-900 dark:text-white">{{ formatRestSeconds(exercise.pivot.rest_seconds) }}</p>
                                    </div>
                                </div>

                                <p v-if="exercise.pivot.notes" class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">
                                    <span class="font-medium">Obs:</span> {{ exercise.pivot.notes }}
                                </p>
                            </div>
                        </div>

                        <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                            <Dumbbell class="mx-auto mb-3 h-12 w-12 text-neutral-300 dark:text-neutral-600" />
                            <p>Nenhum exercício adicionado</p>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center rounded-xl border border-dashed border-neutral-300 bg-neutral-50 py-16 text-center dark:border-neutral-700 dark:bg-neutral-900/50">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                        <Dumbbell class="h-8 w-8 text-neutral-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nenhum treino criado</h3>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                        Este aluno ainda não possui um treino ativo
                    </p>
                    <Link
                        v-if="student?.id"
                        :href="workouts.url({ query: { client_id: student.id, create: 'true' } })"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-emerald-600 transition-colors"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Criar Treino</span>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
