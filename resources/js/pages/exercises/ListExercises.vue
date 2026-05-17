<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Dumbbell, Plus } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';
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
import CreateExerciseSheet from '@/pages/exercises/components/CreateExerciseSheet.vue';
import EditExerciseSheet from '@/pages/exercises/components/EditExerciseSheet.vue';
import { destroy } from '@/routes/exercises';

interface Category {
    id: number;
    name: string;
    slug: string;
}

interface Exercise {
    id: number;
    name: string;
    description?: string;
    category: Category | null;
    category_id: number | null;
    muscle_group: string;
    equipment?: string;
    difficulty: string;
    instructions?: string;
    video_url?: string;
    image?: string;
    is_active: boolean;
}

const props = defineProps<{
    title: string;
    exercises: {
        data: Exercise[];
    };
    categories: {
        data: Category[];
    };
}>();

const page = usePage();

const successMessage = ref('');
const selectedCategoryId = ref<string>('');

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

watch(selectedCategoryId, (value) => {
    router.get('/exercises', { category_id: value === 'all' ? undefined : value }, {
        preserveState: true,
        replace: true,
    });
});

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const isViewOpen = ref(false);
const selectedExercise = ref<Exercise | null>(null);

const handleViewClick = (exercise: Exercise) => {
    selectedExercise.value = exercise;
    isViewOpen.value = true;
};

const handleEditClick = (exercise: Exercise) => {
    selectedExercise.value = exercise;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este exercício?')) {
        return;
    }

    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedExercise.value = null;
        },
    });
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

const exerciseImageUrl = computed(() => {
    if (!selectedExercise.value?.image) {
        return '/images/exercise-placeholder.png';
    }
    return selectedExercise.value.image;
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Exercícios',
                href: '/exercises',
            },
        ],
    },
});
</script>

<template>
    <Head title="Exercícios" />

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
                <h1>Lista de Exercícios</h1>
            </div>

            <Dialog v-model:open="isCreateOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus class="w-4 h-4 mr-2" />
                        Novo Exercício
                    </Button>
                </DialogTrigger>

                <DialogContent class="max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Criar Exercício</DialogTitle>
                    </DialogHeader>

                    <CreateExerciseSheet
                        :categories="categories.data"
                        @close="closeCreateSheet"
                    />
                </DialogContent>
            </Dialog>
        </div>

        <div class="flex gap-4 mb-4">
            <div class="w-64">
                <Select v-model="selectedCategoryId">
                    <SelectTrigger>
                        <SelectValue placeholder="Filtrar por grupo muscular" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Todos os grupos</SelectItem>
                        <SelectItem
                            v-for="category in categories.data"
                            :key="category.id"
                            :value="String(category.id)"
                        >
                            {{ category.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <div
                v-for="exercise in props.exercises.data"
                :key="exercise.id"
                class="flex flex-col md:flex-row md:items-center md:justify-between rounded-lg border bg-neutral-100 dark:bg-neutral-900 p-4 cursor-pointer hover:bg-neutral-200 dark:hover:bg-neutral-800 transition-colors"
                @click="handleViewClick(exercise)"
            >
                <div class="flex flex-col flex-1 mb-4 md:mb-0">
                    <h3 class="font-semibold">{{ exercise.name }}</h3>
                    <p class="text-sm text-neutral-500">
                        {{ exercise.category?.name || exercise.muscle_group }}
                        <span v-if="exercise.equipment">
                            • {{ exercise.equipment }}
                        </span>
                    </p>
                    <p class="text-sm text-neutral-500">
                        <span
                            class="px-2 py-1 rounded text-xs font-medium"
                            :class="{
                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200':
                                    exercise.difficulty === 'Beginner',
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                                    exercise.difficulty === 'Intermediate',
                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                                    exercise.difficulty === 'Advanced',
                            }"
                        >
                            {{ exercise.difficulty }}
                        </span>
                    </p>
                    <p
                        v-if="exercise.description"
                        class="text-sm text-neutral-600 dark:text-neutral-400 mt-2"
                    >
                        {{ exercise.description }}
                    </p>
                </div>
                <div class="flex flex-col md:flex-row gap-2">
                    <Button
                        size="sm"
                        variant="outline"
                        @click.stop="handleEditClick(exercise)"
                    >
                        Editar
                    </Button>
                    <Button
                        size="sm"
                        variant="destructive"
                        @click.stop="handleDeleteClick(exercise.id)"
                    >
                        Deletar
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="!props.exercises.data.length"
            class="py-8 text-center text-neutral-500"
        >
            Nenhum exercício cadastrado
        </div>

        <Dialog v-model:open="isViewOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ selectedExercise?.name }}</DialogTitle>
                </DialogHeader>

                <div v-if="selectedExercise" class="space-y-4">
                    <div class="w-full h-64 bg-neutral-200 dark:bg-neutral-700 rounded-lg overflow-hidden">
                        <img
                            :src="exerciseImageUrl"
                            :alt="selectedExercise.name"
                            class="w-full h-full object-cover"
                            @error="($event.target as HTMLImageElement).src = '/images/exercise-placeholder.png'"
                        />
                    </div>

                    <div>
                        <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Descrição</h4>
                        <p class="text-sm mt-1">{{ selectedExercise.description || 'Sem descrição' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Categoria</h4>
                            <p class="text-sm mt-1">{{ selectedExercise.category?.name || selectedExercise.muscle_group }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Equipamento</h4>
                            <p class="text-sm mt-1">{{ selectedExercise.equipment || 'Não informado' }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Dificuldade</h4>
                            <p class="text-sm mt-1">
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium"
                                    :class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200':
                                            selectedExercise.difficulty === 'Beginner',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                                            selectedExercise.difficulty === 'Intermediate',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                                            selectedExercise.difficulty === 'Advanced',
                                    }"
                                >
                                    {{ selectedExercise.difficulty }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div v-if="selectedExercise.instructions">
                        <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Instruções</h4>
                        <p class="text-sm mt-1 whitespace-pre-line">{{ selectedExercise.instructions }}</p>
                    </div>

                    <div v-if="selectedExercise.video_url">
                        <h4 class="font-semibold text-sm text-neutral-500 dark:text-neutral-400">Vídeo</h4>
                        <a :href="selectedExercise.video_url" target="_blank" class="text-sm mt-1 text-blue-600 hover:underline">
                            Assistir vídeo
                        </a>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isEditOpen">
            <DialogContent class="max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Editar Exercício</DialogTitle>
                </DialogHeader>

                <EditExerciseSheet
                    v-if="selectedExercise"
                    :exercise="selectedExercise"
                    :categories="categories.data"
                    @close="closeEditSheet"
                />
            </DialogContent>
        </Dialog>
    </div>
</template>
