<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Treinos',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ChevronRight, Dumbbell, Plus, Search } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
import CreateWorkoutSheet from '@/pages/workouts/components/CreateWorkoutSheet.vue';
import EditWorkoutSheet from '@/pages/workouts/components/EditWorkoutSheet.vue';
import ViewWorkoutSheet from '@/pages/workouts/components/ViewWorkoutSheet.vue';
import { destroy } from '@/routes/workouts';

interface Student {
    id: number;
    name: string;
    nickname?: string;
}

interface Exercise {
    id: number;
    name: string;
    category?: {
        id: number;
        name: string;
    } | null;
}

interface Category {
    id: number;
    name: string;
    slug: string;
}

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
        order: number;
        notes?: string;
    };
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    student?: {
        id: number;
        name: string;
        nickname?: string;
    };
    student_id: number;
    trainer?: {
        id: number;
        name: string;
    };
    trainer_id: number;
    exercises?: WorkoutExercise[];
    is_active: boolean;
}

const props = defineProps<{
    title: string;
    studentId?: number;
    student?: Student;
    workouts: {
        data: Workout[];
    };
    exercises: {
        data: Exercise[];
    };
    categories: {
        data: Category[];
    };
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const searchQuery = ref('');

const filteredWorkouts = computed(() => {
    let result = props.workouts.data;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (w) =>
                w.name.toLowerCase().includes(query) ||
                w.student?.name.toLowerCase().includes(query) ||
                w.description?.toLowerCase().includes(query),
        );
    }

    return result;
});

const stats = computed(() => ({
    total: props.workouts.data.length,
    totalExercises: props.workouts.data.reduce((sum, w) => sum + (w.exercises?.length || 0), 0),
}));

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
    { immediate: true },
);

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const isViewOpen = ref(false);
const selectedWorkout = ref<Workout | null>(null);
const preSelectedStudentId = ref<string>('');

const handleViewClick = (workout: Workout) => {
    selectedWorkout.value = workout;
    isViewOpen.value = true;
};

const handleEditClick = (workout: Workout) => {
    selectedWorkout.value = workout;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este treino?')) {
        return;
    }

    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedWorkout.value = null;
        },
    });
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
    preSelectedStudentId.value = '';
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

const closeViewSheet = () => {
    isViewOpen.value = false;
    selectedWorkout.value = null;
};

onMounted(() => {
    const url = new URL(window.location.href);
    const shouldCreate = url.searchParams.get('create');

    preSelectedStudentId.value = String(props.studentId);

    if (shouldCreate === 'true') {
        isCreateOpen.value = true;
    }
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
    <Head title="Treinos" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Treinos de {{ props.student?.name ?? 'Aluno' }}</h1>
                    <p class="text-sm text-emerald-100 mt-1">Gerencie os treinos do aluno</p>
                </div>

                <Sheet v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button class="bg-white text-emerald-700 hover:bg-emerald-50 border-0 shadow-lg">
                            <Plus class="w-4 h-4 mr-2" />
                            Novo Treino
                        </Button>
                    </SheetTrigger>

                    <SheetContent side="right" class="w-full sm:max-w-xl p-0">
                        <SheetHeader class="sr-only">
                            <SheetTitle>Criar Treino</SheetTitle>
                        </SheetHeader>

                        <CreateWorkoutSheet
                            v-if="student"
                            :student="student"
                            :exercises="exercises.data"
                            :categories="categories.data"
                            @close="closeCreateSheet"
                        />
                    </SheetContent>
                </Sheet>
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
            <div class="flex flex-col sm:flex-row gap-3 mb-5">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Buscar treino..."
                        class="pl-9 bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700"
                    />
                </div>
            </div>

            <div v-if="filteredWorkouts.length" class="space-y-3">
                <div
                    v-for="workout in filteredWorkouts"
                    :key="workout.id"
                    class="group bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden cursor-pointer transition-all hover:border-emerald-300 hover:shadow-lg dark:hover:border-emerald-600"
                    @click="handleViewClick(workout)"
                >
                    <div class="flex items-center gap-4 p-4">
                        <div
                            :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white font-bold text-sm shadow-sm', getAvatarColor(workout.student_id || workout.id)]"
                        >
                            {{ getInitials(workout.student?.name || workout.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-neutral-900 dark:text-white truncate">{{ workout.name }}</h3>
                                <span v-if="workout.exercises?.length" class="shrink-0 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    {{ workout.exercises.length }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ workout.student?.name || 'Sem aluno' }}</span>
                                <span v-if="workout.description" class="text-xs text-neutral-400 dark:text-neutral-500 truncate">
                                    • {{ workout.description }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <Button
                                size="sm"
                                variant="ghost"
                                class="h-8 w-8 p-0"
                                @click.stop="handleEditClick(workout)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                            </Button>
                            <Button
                                size="sm"
                                variant="ghost"
                                class="h-8 w-8 p-0"
                                @click.stop="handleDeleteClick(workout.id)"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            </Button>
                            <ChevronRight class="h-5 w-5 text-neutral-300 dark:text-neutral-600 group-hover:text-emerald-500 transition-colors" />
                        </div>
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
                    {{ searchQuery ? 'Tente buscar com outros termos' : 'Clique em "Novo Treino" para começar' }}
                </p>
            </div>
        </div>
    </div>

    <Sheet v-model:open="isViewOpen">
        <SheetContent side="right" class="w-full sm:max-w-md p-0">
            <SheetHeader class="sr-only">
                <SheetTitle>{{ selectedWorkout?.name }}</SheetTitle>
            </SheetHeader>

            <ViewWorkoutSheet
                v-if="selectedWorkout"
                :workout="selectedWorkout"
            />
        </SheetContent>
    </Sheet>

    <Dialog v-model:open="isEditOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Treino</DialogTitle>
            </DialogHeader>

            <EditWorkoutSheet
                v-if="selectedWorkout && student"
                :workout="selectedWorkout"
                :student="student"
                :exercises="exercises.data"
                @close="closeEditSheet"
            />
        </DialogContent>
    </Dialog>
</template>
