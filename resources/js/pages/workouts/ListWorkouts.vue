<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Dumbbell, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import CreateWorkoutSheet from '@/pages/workouts/components/CreateWorkoutSheet.vue';
import EditWorkoutSheet from '@/pages/workouts/components/EditWorkoutSheet.vue';
import ViewWorkoutSheet from '@/pages/workouts/components/ViewWorkoutSheet.vue';
import { destroy } from '@/routes/workouts';

interface Client {
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

const props = defineProps<{
    title: string;
    workouts: {
        data: Workout[];
    };
    clients: {
        data: Client[];
    };
    exercises: {
        data: Exercise[];
    };
    categories: {
        data: Category[];
    };
}>();

const page = usePage();

const successMessage = ref('');
const selectedClientId = ref<string>('');

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            successMessage.value = flash.success;
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        }
    },
    { immediate: true },
);

watch(selectedClientId, (value) => {
    router.get('/workouts', { client_id: value === 'all' ? undefined : value }, {
        preserveState: true,
        replace: true,
    });
});

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const isViewOpen = ref(false);
const selectedWorkout = ref<Workout | null>(null);

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
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

const closeViewSheet = () => {
    isViewOpen.value = false;
    selectedWorkout.value = null;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Treinos',
                href: '/workouts',
            },
        ],
    },
});
</script>

<template>
    <Head title="Treinos" />

    <div
        v-if="successMessage"
        class="fixed top-4 right-4 rounded-lg bg-green-500 px-4 py-2 text-white shadow-lg"
    >
        {{ successMessage }}
    </div>

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <div class="py-5 font-extrabold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <Dumbbell class="w-6 h-6" />
                <h1>Lista de Treinos</h1>
            </div>

            <Dialog v-model:open="isCreateOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus class="w-4 h-4 mr-2" />
                        Novo Treino
                    </Button>
                </DialogTrigger>

                <DialogContent class="max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Criar Treino</DialogTitle>
                    </DialogHeader>

                    <CreateWorkoutSheet
                        :clients="clients.data"
                        :exercises="exercises.data"
                        :categories="categories.data"
                        @close="closeCreateSheet"
                    />
                </DialogContent>
            </Dialog>
        </div>

        <div class="flex gap-4 mb-4">
            <div class="w-64">
                <Select v-model="selectedClientId">
                    <SelectTrigger>
                        <SelectValue placeholder="Filtrar por aluno" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Todos os alunos</SelectItem>
                        <SelectItem
                            v-for="client in clients.data"
                            :key="client.id"
                            :value="String(client.id)"
                        >
                            {{ client.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <div
                v-for="workout in props.workouts.data"
                :key="workout.id"
                class="group flex flex-col md:flex-row md:items-center md:justify-between rounded-xl border border-neutral-200 bg-white p-5 cursor-pointer transition-all hover:border-emerald-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                @click="handleViewClick(workout)"
            >
                <div class="flex flex-col flex-1 mb-3 md:mb-0">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600">
                            <Dumbbell class="h-4 w-4 text-white" />
                        </div>
                        <h3 class="font-semibold text-neutral-900 dark:text-white">{{ workout.name }}</h3>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 pl-10">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ workout.client?.name || 'N/A' }}
                        </p>
                        <span v-if="workout.exercises?.length" class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            {{ workout.exercises.length }} exercício(s)
                        </span>
                    </div>
                    <p
                        v-if="workout.description"
                        class="text-sm text-neutral-500 dark:text-neutral-400 mt-2 pl-10 line-clamp-1"
                    >
                        {{ workout.description }}
                    </p>
                </div>
                <div class="flex flex-row md:flex-col gap-2 pl-10 md:pl-0">
                    <Button
                        size="sm"
                        variant="outline"
                        @click.stop="handleEditClick(workout)"
                    >
                        Editar
                    </Button>
                    <Button
                        size="sm"
                        variant="destructive"
                        @click.stop="handleDeleteClick(workout.id)"
                    >
                        Deletar
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="!props.workouts.data.length"
            class="flex flex-col items-center justify-center py-12 text-center"
        >
            <Dumbbell class="mb-3 h-12 w-12 text-neutral-300 dark:text-neutral-600" />
            <p class="text-neutral-500 dark:text-neutral-400">Nenhum treino cadastrado</p>
            <p class="text-sm text-neutral-400 dark:text-neutral-500 mt-1">Clique em "Novo Treino" para começar</p>
        </div>

        <Sheet v-model:open="isViewOpen">
            <SheetContent side="right" class="w-full sm:max-w-md p-0">
                <SheetHeader class="sr-only">
                    <SheetTitle>{{ selectedWorkout?.name }}</SheetTitle>
                </SheetHeader>

                <ViewWorkoutSheet
                    v-if="selectedWorkout"
                    :workout="selectedWorkout"
                    @close="closeViewSheet"
                />
            </SheetContent>
        </Sheet>

        <Dialog v-model:open="isEditOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Editar Treino</DialogTitle>
                </DialogHeader>

                <EditWorkoutSheet
                    v-if="selectedWorkout"
                    :workout="selectedWorkout"
                    :clients="clients.data"
                    :exercises="exercises.data"
                    @close="closeEditSheet"
                />
            </DialogContent>
        </Dialog>
    </div>
</template>
