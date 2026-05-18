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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import CreateWorkoutSheet from '@/pages/workouts/components/CreateWorkoutSheet.vue';
import EditWorkoutSheet from '@/pages/workouts/components/EditWorkoutSheet.vue';
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

        <div class="flex flex-col gap-4">
            <div
                v-for="workout in props.workouts.data"
                :key="workout.id"
                class="flex flex-col md:flex-row md:items-center md:justify-between rounded-lg border bg-neutral-100 dark:bg-neutral-900 p-4 cursor-pointer hover:bg-neutral-200 dark:hover:bg-neutral-800 transition-colors"
                @click="handleViewClick(workout)"
            >
                <div class="flex flex-col flex-1 mb-4 md:mb-0">
                    <h3 class="font-semibold">{{ workout.name }}</h3>
                    <p class="text-sm text-neutral-500">
                        Aluno: {{ workout.client?.name || 'N/A' }}
                        <span v-if="workout.exercises?.length">
                            • {{ workout.exercises.length }} exercício(s)
                        </span>
                    </p>
                    <p
                        v-if="workout.description"
                        class="text-sm text-neutral-600 dark:text-neutral-400 mt-2"
                    >
                        {{ workout.description }}
                    </p>
                </div>
                <div class="flex flex-col md:flex-row gap-2">
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
            class="py-8 text-center text-neutral-500"
        >
            Nenhum treino cadastrado
        </div>

        <Dialog v-model:open="isViewOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ selectedWorkout?.name }}</DialogTitle>
                </DialogHeader>

                <div v-if="selectedWorkout" class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Aluno</h4>
                        <p class="text-sm mt-1">{{ selectedWorkout.client?.name || 'N/A' }}</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Descrição</h4>
                        <p class="text-sm mt-1">{{ selectedWorkout.description || 'Sem descrição' }}</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400 mb-2">Exercícios</h4>
                        <div v-if="selectedWorkout.exercises?.length" class="space-y-2">
                            <div
                                v-for="exercise in selectedWorkout.exercises"
                                :key="exercise.id"
                                class="p-3 border rounded-lg"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">{{ exercise.name }}</span>
                                    <span class="text-xs text-neutral-500">
                                        {{ exercise.category?.name || '' }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                                    <div>
                                        <span class="text-neutral-500">Séries:</span>
                                        <span class="ml-1">{{ exercise.pivot.sets }}</span>
                                    </div>
                                    <div>
                                        <span class="text-neutral-500">Reps:</span>
                                        <span class="ml-1">{{ exercise.pivot.reps }}</span>
                                    </div>
                                    <div>
                                        <span class="text-neutral-500">Descanso:</span>
                                        <span class="ml-1">{{ exercise.pivot.rest_seconds }}s</span>
                                    </div>
                                </div>
                                <p v-if="exercise.pivot.notes" class="text-xs text-neutral-500 mt-1">
                                    {{ exercise.pivot.notes }}
                                </p>
                            </div>
                        </div>
                        <p v-else class="text-sm text-neutral-500">Nenhum exercício adicionado</p>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

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
