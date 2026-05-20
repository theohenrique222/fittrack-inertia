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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Calendar,
    Dumbbell,
    Edit,
    Mail,
    MoreVertical,
    Play,
    Trash2,
    User,
    Activity,
    TrendingUp,
    Clock,
    ChevronRight,
    Search,
    Plus,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
import { destroy as destroyStudent, resetPassword } from '@/routes/students';
import { destroy as destroyWorkout } from '@/routes/workouts';
import CreateWorkoutSheet from '@/pages/workouts/components/CreateWorkoutSheet.vue';
import EditWorkoutSheet from '@/pages/workouts/components/EditWorkoutSheet.vue';

const page = usePage();
const { toasts, success, error } = useToast();

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            success(flash.success);
        }

        if (flash?.error) {
            error(flash.error);
        }
    },
);

interface Student {
    id: number;
    name: string;
    email: string;
    nickname?: string;
}

interface ExerciseOption {
    id: number;
    name: string;
    category?: {
        id: number;
        name: string;
    } | null;
}

interface CategoryOption {
    id: number;
    name: string;
    slug: string;
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
    client_id?: number;
    student_id?: number;
    exercises?: Exercise[];
    created_at?: string;
}

interface Stats {
    total_workouts: number;
    active_workouts: number;
    created_at: string;
    trainer_name: string;
}

const props = defineProps<{
    title: string;
    student: Student;
    workout: Workout | null;
    workouts: Workout[];
    stats: Stats;
    exercises: ExerciseOption[];
    categories: CategoryOption[];
}>();

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const searchQuery = ref('');
const selectedWorkout = ref<Workout | null>(null);

const activeTab = ref<'overview' | 'workout' | 'history'>('overview');

const filteredWorkouts = computed(() => {
    if (!searchQuery.value) return props.workouts;

    const query = searchQuery.value.toLowerCase();
    return props.workouts.filter(
        (w) =>
            w.name.toLowerCase().includes(query) ||
            w.description?.toLowerCase().includes(query),
    );
});

const tabs = computed(() => [
    { id: 'overview' as const, label: 'Visão Geral' },
    { id: 'workout' as const, label: 'Treino Ativo' },
    { id: 'history' as const, label: 'Histórico' },
]);

function getInitials(name: string | undefined | null): string {
    if (!name) return '';
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

function formatRestSeconds(seconds: number): string {
    if (seconds < 60) {
        return `${seconds}s`;
    }

    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    return remainingSeconds > 0 ? `${minutes}m ${remainingSeconds}s` : `${minutes}m`;
}

function handleDelete() {
    if (!confirm('Tem certeza que deseja deletar este aluno?')) {
        return;
    }

    router.delete(destroyStudent.url({ student: props.student.id }), {
        onSuccess: () => router.visit('/students'),
    });
}

function handleDeleteWorkout(workoutId: number) {
    if (!confirm('Tem certeza que deseja deletar este treino?')) {
        return;
    }

    router.delete(destroyWorkout.url({ workout: workoutId }), {
        preserveScroll: true,
    });
}

function handleResetPassword() {
    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) {
        return;
    }

    router.post(resetPassword.url({ student: props.student.id }));
}

function handleEditWorkout(workout: Workout) {
    selectedWorkout.value = {
        ...workout,
        student_id: workout.client_id || props.student.id,
    };
    isEditOpen.value = true;
}

function closeEditSheet() {
    isEditOpen.value = false;
    selectedWorkout.value = null;
}

const avatarColors = [
    'from-emerald-400 to-emerald-600',
    'from-blue-400 to-blue-600',
    'from-violet-400 to-violet-600',
    'from-orange-400 to-orange-600',
    'from-pink-400 to-pink-600',
];

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}
</script>

<template>
    <Head :title="title" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <!-- Header com perfil do aluno -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 p-6 dark:from-neutral-800 dark:via-neutral-700 dark:to-neutral-800">
            <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/students" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20">
                        <ArrowLeft class="h-5 w-5" />
                    </Link>

                    <div class="flex items-center gap-4">
                        <div :class="['flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br text-3xl font-bold text-white shadow-xl', getAvatarColor(student.id)]">
                            {{ getInitials(student.name) }}
                        </div>

                        <div>
                            <div class="flex items-center gap-3">
                                <h1 class="text-2xl font-bold text-white">{{ student.name }}</h1>
                                <Badge v-if="student.nickname" variant="secondary" class="bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30">
                                    {{ student.nickname }}
                                </Badge>
                            </div>
                            <div class="mt-1 flex items-center gap-4 text-sm text-neutral-300">
                                <span class="flex items-center gap-1.5">
                                    <Mail class="h-4 w-4" />
                                    {{ student.email }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <Calendar class="h-4 w-4" />
                                    Aluno desde {{ stats.created_at }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 lg:flex-col">
                    <Button
                        variant="default"
                        class="bg-emerald-500 text-white hover:bg-emerald-600"
                        @click="workout ? handleEditWorkout(workout) : isCreateOpen = true"
                    >
                        <Dumbbell class="mr-2 h-4 w-4" />
                        {{ workout ? 'Atualizar Treino' : 'Novo Treino' }}
                    </Button>

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" class="bg-white/10 text-white hover:bg-white/20">
                                <MoreVertical class="h-4 w-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem @click="router.visit(`/students/${student.id}/edit`)">
                                <Edit class="mr-2 h-4 w-4" />
                                Editar Aluno
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="handleResetPassword">
                                <User class="mr-2 h-4 w-4" />
                                Redefinir Senha
                            </DropdownMenuItem>
                            <DropdownMenuItem class="text-red-600" @click="handleDelete">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Deletar Aluno
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                        <Dumbbell class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ stats.total_workouts }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Total de Treinos</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                        <Activity class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ stats.active_workouts }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Treinos Ativos</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30">
                        <TrendingUp class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workout ? workout.exercises?.length || 0 : 0 }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Exercícios Ativos</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                        <Clock class="h-5 w-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ stats.created_at?.split('/')[1] || '-' }}</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Mês de Início</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                    'flex-1 rounded-md px-4 py-2 text-sm font-medium transition-all',
                    activeTab === tab.id
                        ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-700 dark:text-white'
                        : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white',
                ]"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- Tab Content: Visão Geral -->
        <div v-if="activeTab === 'overview'" class="space-y-6">
            <!-- Treino Ativo Preview -->
            <div v-if="workout" class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div
                    class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700 cursor-pointer hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-colors"
                    @click="router.visit(`/workouts/${workout.id}`)"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600">
                                <Dumbbell class="h-5 w-5 text-white" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-neutral-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                    {{ workout.name }}
                                </h3>
                                <p v-if="workout.description" class="text-sm text-neutral-500 dark:text-neutral-400 truncate">
                                    {{ workout.description }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-3">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="h-8 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20"
                                @click.stop="handleEditWorkout(workout)"
                            >
                                <Edit class="mr-1.5 h-3.5 w-3.5" />
                                Editar
                            </Button>
                            <Badge class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                Ativo
                            </Badge>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="workout.exercises && workout.exercises.length > 0" class="space-y-3">
                        <div
                            v-for="(exercise, index) in workout.exercises.slice(0, 3)"
                            :key="exercise.id"
                            class="flex items-center gap-4 rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-900/50 cursor-pointer hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors"
                            @click="router.visit(`/workouts/${workout.id}`)"
                        >
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                {{ index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-medium text-neutral-900 dark:text-white">{{ exercise.name }}</h4>
                                <p v-if="exercise.category" class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ exercise.category.name }}
                                </p>
                            </div>
                            <div class="flex shrink-0 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Séries</p>
                                    <p class="text-lg font-semibold text-neutral-900 dark:text-white">{{ exercise.pivot.sets }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Reps</p>
                                    <p class="text-lg font-semibold text-neutral-900 dark:text-white">{{ exercise.pivot.reps }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Descanso</p>
                                    <p class="text-lg font-semibold text-neutral-900 dark:text-white">{{ formatRestSeconds(exercise.pivot.rest_seconds) }}</p>
                                </div>
                            </div>
                        </div>

                        <Button
                            v-if="workout.exercises.length > 3"
                            variant="ghost"
                            class="w-full"
                            @click="activeTab = 'workout'"
                        >
                            Ver todos os {{ workout.exercises.length }} exercícios
                        </Button>
                    </div>

                    <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                        <Dumbbell class="mx-auto mb-3 h-12 w-12 text-neutral-300 dark:text-neutral-600" />
                        <p>Nenhum exercício adicionado</p>
                    </div>
                </div>

                <div class="border-t border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900/50">
                    <Button
                        variant="default"
                        class="w-full bg-emerald-500 text-white hover:bg-emerald-600"
                        @click="router.visit(`/workouts/${workout.id}`)"
                    >
                        <Play class="mr-2 h-4 w-4" />
                        Ver Treino Completo
                    </Button>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-neutral-300 bg-neutral-50 py-16 text-center dark:border-neutral-700 dark:bg-neutral-900/50">
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                    <Dumbbell class="h-8 w-8 text-neutral-400" />
                </div>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Nenhum treino criado</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    Este aluno ainda não possui um treino ativo
                </p>
                <Button
                    size="lg"
                    class="mt-6 bg-emerald-500 px-8 text-white hover:bg-emerald-600 shadow-lg shadow-emerald-500/25"
                    @click="isCreateOpen = true"
                >
                    <Plus class="mr-2 h-5 w-5" />
                    Criar Primeiro Treino
                </Button>
            </div>
        </div>

        <!-- Tab Content: Treino Ativo -->
        <div v-if="activeTab === 'workout'" class="space-y-4">
            <div v-if="workouts.length > 0" class="space-y-4">
                <div
                    v-for="w in workouts"
                    :key="w.id"
                    class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <!-- Header -->
                    <div class="border-b border-neutral-200 bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-5 dark:border-neutral-700 dark:from-emerald-900/20 dark:to-teal-900/20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 shadow-lg">
                                    <Dumbbell class="h-6 w-6 text-white" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-bold text-neutral-900 dark:text-white">
                                            {{ w.name }}
                                        </h3>
                                        <Badge v-if="w.is_active" class="bg-emerald-500 text-white">
                                            Ativo
                                        </Badge>
                                    </div>
                                    <p v-if="w.description" class="text-sm text-neutral-500 dark:text-neutral-400 truncate">
                                        {{ w.description }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <Button
                                    v-if="w.is_active"
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/20"
                                    @click="handleEditWorkout(w)"
                                >
                                    <Edit class="mr-1.5 h-3.5 w-3.5" />
                                    Editar
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="h-8 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                                    @click="handleDeleteWorkout(w.id)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3 px-6 py-4">
                        <Button
                            variant="default"
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white hover:from-emerald-600 hover:to-teal-600 shadow-lg shadow-emerald-500/20"
                            @click="router.visit(`/workouts/${w.id}`)"
                        >
                            <Play class="mr-2 h-4 w-4" />
                            Iniciar Treino
                        </Button>
                        <Button
                            variant="outline"
                            class="border-neutral-300 text-neutral-700 hover:bg-neutral-100 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800"
                            @click="router.visit(`/workouts/${w.id}`)"
                        >
                            Ver Detalhes
                            <ChevronRight class="ml-2 h-4 w-4" />
                        </Button>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-300 bg-neutral-50 py-20 text-center dark:border-neutral-700 dark:bg-neutral-900/50">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-700">
                    <Dumbbell class="h-10 w-10 text-neutral-400" />
                </div>
                <h3 class="text-xl font-bold text-neutral-900 dark:text-white">Nenhum treino ativo</h3>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400">
                    Crie um treino para este aluno começar
                </p>
            </div>

            <!-- Novo Treino Button -->
            <Button
                size="lg"
                class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white hover:from-emerald-600 hover:to-teal-600 shadow-lg shadow-emerald-500/25"
                @click="isCreateOpen = true"
            >
                <Plus class="mr-2 h-5 w-5" />
                Criar Novo Treino
            </Button>
        </div>

        <!-- Tab Content: Histórico -->
        <div v-if="activeTab === 'history'" class="space-y-4">
            <div class="relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
                <Input
                    v-model="searchQuery"
                    placeholder="Buscar treino..."
                    class="pl-9 bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700"
                />
            </div>

            <div v-if="filteredWorkouts.length > 0" class="space-y-3">
                <div
                    v-for="workout in filteredWorkouts"
                    :key="workout.id"
                    class="group bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden transition-all hover:border-emerald-300 hover:shadow-lg dark:hover:border-emerald-600"
                >
                    <div
                        class="flex items-center gap-4 p-4 cursor-pointer"
                        @click="router.visit(`/workouts/${workout.id}`)"
                    >
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white font-bold text-sm shadow-sm">
                            {{ (workout.exercises?.length || 0).toString().padStart(2, '0') }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-neutral-900 dark:text-white truncate">{{ workout.name }}</h3>
                                <span
                                    v-if="workout.is_active"
                                    class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                                >
                                    Ativo
                                </span>
                            </div>
                            <p v-if="workout.description" class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5 truncate">
                                {{ workout.description }}
                            </p>
                            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">
                                {{ workout.exercises?.length || 0 }} exercícios
                            </p>
                        </div>

                        <ChevronRight class="h-5 w-5 text-neutral-300 dark:text-neutral-600 group-hover:text-emerald-500 transition-colors shrink-0" />
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-neutral-300 bg-neutral-50 py-16 text-center dark:border-neutral-700 dark:bg-neutral-900/50"
            >
                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                    <Dumbbell class="h-8 w-8 text-neutral-400" />
                </div>
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    {{ searchQuery ? 'Nenhum treino encontrado' : 'Nenhum treino cadastrado' }}
                </h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    {{ searchQuery ? 'Tente buscar com outros termos' : 'Crie o primeiro treino do aluno' }}
                </p>
                <Button
                    size="lg"
                    class="mt-6 bg-emerald-500 px-8 text-white hover:bg-emerald-600 shadow-lg shadow-emerald-500/25"
                    @click="isCreateOpen = true"
                >
                    <Plus class="mr-2 h-5 w-5" />
                    Criar Primeiro Treino
                </Button>
            </div>
        </div>
    </div>

    <Sheet v-model:open="isCreateOpen">
        <SheetContent side="right" class="w-full sm:max-w-xl p-0">
            <SheetHeader class="sr-only">
                <SheetTitle>Criar Treino</SheetTitle>
            </SheetHeader>

            <CreateWorkoutSheet
                v-if="student"
                :student="student"
                :exercises="exercises"
                :categories="categories"
                :redirect-on-success="`/students/${student.id}`"
                @close="isCreateOpen = false"
            />
        </SheetContent>
    </Sheet>

    <Sheet v-model:open="isEditOpen">
        <SheetContent side="right" class="w-full sm:max-w-xl p-0">
            <SheetHeader class="sr-only">
                <SheetTitle>Editar Treino</SheetTitle>
            </SheetHeader>

            <EditWorkoutSheet
                v-if="selectedWorkout && student"
                :workout="selectedWorkout"
                :student="student"
                :exercises="exercises"
                @close="closeEditSheet"
            />
        </SheetContent>
    </Sheet>

    <ToastContainer v-model="toasts" />
</template>
