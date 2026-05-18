<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Exercícios',
                href: '/exercises',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    ChevronRight,
    Dumbbell,
    Plus,
    Search,
    Trophy,
    Zap,
    ArmFlex,
    Footprints,
    Activity,
    Target,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
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
const searchQuery = ref('');
const selectedDifficulty = ref<string>('all');

const filteredExercises = computed(() => {
    let result = props.exercises.data;

    if (selectedDifficulty.value !== 'all') {
        result = result.filter(
            (e) => e.difficulty === selectedDifficulty.value,
        );
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (e) =>
                e.name.toLowerCase().includes(query) ||
                e.description?.toLowerCase().includes(query) ||
                e.category?.name.toLowerCase().includes(query) ||
                e.muscle_group.toLowerCase().includes(query) ||
                e.equipment?.toLowerCase().includes(query),
        );
    }

    return result;
});

const stats = computed(() => ({
    total: props.exercises.data.length,
    active: props.exercises.data.filter((e) => e.is_active).length,
    categories: new Set(props.exercises.data.map((e) => e.category_id)).size,
}));

const groupedExercises = computed(() => {
    const exercises = filteredExercises.value;
    const groups: Record<string, { category: Category | null; exercises: Exercise[] }> = {};

    const categoryOrder = ['chest', 'back', 'shoulders', 'biceps', 'triceps', 'forearms', 'abs', 'quadriceps', 'hamstrings', 'glutes', 'calves', 'full-body'];

    for (const exercise of exercises) {
        const slug = exercise.category?.slug || 'uncategorized';
        const key = exercise.category ? `cat-${exercise.category.id}` : 'uncategorized';

        if (!groups[key]) {
            groups[key] = {
                category: exercise.category || null,
                exercises: [],
            };
        }

        groups[key].exercises.push(exercise);
    }

    const sortedKeys = Object.keys(groups).sort((a, b) => {
        if (a === 'uncategorized') {
return 1;
}

        if (b === 'uncategorized') {
return -1;
}

        const slugA = groups[a].category?.slug || '';
        const slugB = groups[b].category?.slug || '';

        const indexA = categoryOrder.indexOf(slugA);
        const indexB = categoryOrder.indexOf(slugB);

        if (indexA === -1 && indexB === -1) {
return slugA.localeCompare(slugB);
}

        if (indexA === -1) {
return 1;
}

        if (indexB === -1) {
return -1;
}

        return indexA - indexB;
    });

    return sortedKeys.map((key) => ({
        key,
        category: groups[key].category,
        exercises: groups[key].exercises,
    }));
});

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

function getDifficultyColor(difficulty: string): string {
    switch (difficulty) {
        case 'Beginner':
            return 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300';
        case 'Intermediate':
            return 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300';
        case 'Advanced':
            return 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300';
        default:
            return 'bg-neutral-50 text-neutral-700 dark:bg-neutral-900/30 dark:text-neutral-300';
    }
}

function getDifficultyLabel(difficulty: string): string {
    switch (difficulty) {
        case 'Beginner':
            return 'Iniciante';
        case 'Intermediate':
            return 'Intermediário';
        case 'Advanced':
            return 'Avançado';
        default:
            return difficulty;
    }
}

const avatarColors = [
    'from-emerald-400 to-emerald-600',
    'from-teal-400 to-teal-600',
    'from-cyan-400 to-cyan-600',
    'from-green-400 to-green-600',
    'from-lime-400 to-lime-600',
];

const muscleGroupColors: Record<string, { bg: string; text: string; border: string; badge: string; icon: string }> = {
    chest: {
        bg: 'bg-red-50 dark:bg-red-950/30',
        text: 'text-red-700 dark:text-red-300',
        border: 'border-red-200 dark:border-red-800',
        badge: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        icon: 'text-red-500',
    },
    back: {
        bg: 'bg-blue-50 dark:bg-blue-950/30',
        text: 'text-blue-700 dark:text-blue-300',
        border: 'border-blue-200 dark:border-blue-800',
        badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        icon: 'text-blue-500',
    },
    shoulders: {
        bg: 'bg-yellow-50 dark:bg-yellow-950/30',
        text: 'text-yellow-700 dark:text-yellow-300',
        border: 'border-yellow-200 dark:border-yellow-800',
        badge: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
        icon: 'text-yellow-500',
    },
    biceps: {
        bg: 'bg-purple-50 dark:bg-purple-950/30',
        text: 'text-purple-700 dark:text-purple-300',
        border: 'border-purple-200 dark:border-purple-800',
        badge: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
        icon: 'text-purple-500',
    },
    triceps: {
        bg: 'bg-pink-50 dark:bg-pink-950/30',
        text: 'text-pink-700 dark:text-pink-300',
        border: 'border-pink-200 dark:border-pink-800',
        badge: 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300',
        icon: 'text-pink-500',
    },
    forearms: {
        bg: 'bg-indigo-50 dark:bg-indigo-950/30',
        text: 'text-indigo-700 dark:text-indigo-300',
        border: 'border-indigo-200 dark:border-indigo-800',
        badge: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
        icon: 'text-indigo-500',
    },
    abs: {
        bg: 'bg-emerald-50 dark:bg-emerald-950/30',
        text: 'text-emerald-700 dark:text-emerald-300',
        border: 'border-emerald-200 dark:border-emerald-800',
        badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        icon: 'text-emerald-500',
    },
    quadriceps: {
        bg: 'bg-orange-50 dark:bg-orange-950/30',
        text: 'text-orange-700 dark:text-orange-300',
        border: 'border-orange-200 dark:border-orange-800',
        badge: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
        icon: 'text-orange-500',
    },
    hamstrings: {
        bg: 'bg-amber-50 dark:bg-amber-950/30',
        text: 'text-amber-700 dark:text-amber-300',
        border: 'border-amber-200 dark:border-amber-800',
        badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        icon: 'text-amber-500',
    },
    glutes: {
        bg: 'bg-rose-50 dark:bg-rose-950/30',
        text: 'text-rose-700 dark:text-rose-300',
        border: 'border-rose-200 dark:border-rose-800',
        badge: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
        icon: 'text-rose-500',
    },
    calves: {
        bg: 'bg-teal-50 dark:bg-teal-950/30',
        text: 'text-teal-700 dark:text-teal-300',
        border: 'border-teal-200 dark:border-teal-800',
        badge: 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300',
        icon: 'text-teal-500',
    },
    'full-body': {
        bg: 'bg-cyan-50 dark:bg-cyan-950/30',
        text: 'text-cyan-700 dark:text-cyan-300',
        border: 'border-cyan-200 dark:border-cyan-800',
        badge: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300',
        icon: 'text-cyan-500',
    },
};

const muscleGroupIcons: Record<string, any> = {
    chest: Target,
    back: ArmFlex,
    shoulders: Zap,
    biceps: ArmFlex,
    triceps: ArmFlex,
    forearms: ArmFlex,
    abs: Activity,
    quadriceps: Footprints,
    hamstrings: Footprints,
    glutes: Footprints,
    calves: Footprints,
    'full-body': Dumbbell,
};

function getMuscleGroupColor(slug: string | undefined) {
    if (!slug) {
return muscleGroupColors['full-body'];
}

    return muscleGroupColors[slug] || muscleGroupColors['full-body'];
}

function getMuscleGroupIcon(slug: string | undefined) {
    if (!slug) {
return Dumbbell;
}

    return muscleGroupIcons[slug] || Dumbbell;
}

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}
</script>

<template>
    <Head title="Exercícios" />

    <div
        v-if="successMessage"
        class="fixed top-4 right-4 z-50 rounded-lg bg-emerald-500 px-4 py-2 text-white shadow-lg"
    >
        {{ successMessage }}
    </div>

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white"
        >
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Exercícios</h1>
                    <p class="mt-1 text-sm text-emerald-100">
                        Gerencie o catálogo de exercícios
                    </p>
                </div>

                <Dialog v-model:open="isCreateOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="border-0 bg-white text-emerald-700 shadow-lg hover:bg-emerald-50"
                        >
                            <Plus class="mr-2 h-4 w-4" />
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

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <Dumbbell class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Total</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <Zap class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Ativos</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.active }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <Trophy class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Categorias</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.categories }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 px-6 py-5 dark:bg-neutral-900">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-neutral-400"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Buscar exercício por nome, categoria ou equipamento..."
                        class="border-neutral-200 bg-white pl-9 dark:border-neutral-700 dark:bg-neutral-800"
                    />
                </div>

                <div class="w-full sm:w-48">
                    <select
                        v-model="selectedDifficulty"
                        class="h-10 w-full rounded-lg border border-neutral-200 bg-white px-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="all">Todas dificuldades</option>
                        <option value="Beginner">Iniciante</option>
                        <option value="Intermediate">Intermediário</option>
                        <option value="Advanced">Avançado</option>
                    </select>
                </div>
            </div>

            <div v-if="groupedExercises.length" class="space-y-6">
                <div
                    v-for="group in groupedExercises"
                    :key="group.key"
                >
                    <div class="mb-3 flex items-center gap-3">
                        <component
                            :is="getMuscleGroupIcon(group.category?.slug)"
                            :class="[
                                'h-5 w-5',
                                getMuscleGroupColor(group.category?.slug).icon,
                            ]"
                        />
                        <h2
                            :class="[
                                'text-lg font-semibold',
                                getMuscleGroupColor(group.category?.slug).text,
                            ]"
                        >
                            {{ group.category?.name || 'Sem Categoria' }}
                        </h2>
                        <span
                            :class="[
                                'ml-auto rounded-full px-2.5 py-0.5 text-xs font-medium',
                                getMuscleGroupColor(group.category?.slug).badge,
                            ]"
                        >
                            {{ group.exercises.length }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="exercise in group.exercises"
                            :key="exercise.id"
                            class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-emerald-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                            @click="handleViewClick(exercise)"
                        >
                            <div class="flex items-center gap-4 p-4">
                                <div
                                    :class="[
                                        'flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br font-semibold text-white shadow-sm',
                                        getAvatarColor(exercise.id),
                                    ]"
                                >
                                    <Dumbbell class="h-4 w-4" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3
                                            class="truncate font-medium text-neutral-900 dark:text-white"
                                        >
                                            {{ exercise.name }}
                                        </h3>
                                        <span
                                            :class="[
                                                'inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                                getDifficultyColor(exercise.difficulty),
                                            ]"
                                        >
                                            {{
                                                getDifficultyLabel(exercise.difficulty)
                                            }}
                                        </span>
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2">
                                        <span
                                            v-if="exercise.equipment"
                                            class="text-xs text-neutral-500 dark:text-neutral-400"
                                        >
                                            {{ exercise.equipment }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <Button
                                        size="sm"
                                        variant="ghost"
                                        class="h-8 w-8 p-0"
                                        @click.stop="handleEditClick(exercise)"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-neutral-500"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"
                                            />
                                            <path d="m15 5 4 4" />
                                        </svg>
                                    </Button>
                                    <Button
                                        size="sm"
                                        variant="ghost"
                                        class="h-8 w-8 p-0"
                                        @click.stop="handleDeleteClick(exercise.id)"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-red-500"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M3 6h18" />
                                            <path
                                                d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"
                                            />
                                            <path
                                                d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"
                                            />
                                            <line x1="10" x2="10" y1="11" y2="17" />
                                            <line x1="14" x2="14" y1="11" y2="17" />
                                        </svg>
                                    </Button>
                                    <ChevronRight
                                        class="h-5 w-5 text-neutral-300 transition-colors group-hover:text-emerald-500 dark:text-neutral-600"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <div
                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800"
                >
                    <Dumbbell
                        class="h-8 w-8 text-neutral-300 dark:text-neutral-600"
                    />
                </div>
                <p class="font-medium text-neutral-600 dark:text-neutral-400">
                    {{
                        searchQuery
                            ? 'Nenhum exercício encontrado'
                            : 'Nenhum exercício cadastrado'
                    }}
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    {{
                        searchQuery
                            ? 'Tente buscar com outros termos'
                            : 'Clique em "Novo Exercício" para começar'
                    }}
                </p>
            </div>
        </div>
    </div>

    <Dialog v-model:open="isViewOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>{{ selectedExercise?.name }}</DialogTitle>
            </DialogHeader>

            <div v-if="selectedExercise" class="space-y-4">
                <div
                    class="h-48 w-full overflow-hidden rounded-lg bg-neutral-200 dark:bg-neutral-700"
                >
                    <img
                        :src="exerciseImageUrl"
                        :alt="selectedExercise.name"
                        class="h-full w-full object-cover"
                        @error="
                            ($event.target as HTMLImageElement).src =
                                '/images/exercise-placeholder.png'
                        "
                    />
                </div>

                <div>
                    <h4
                        class="text-sm font-semibold text-neutral-500 dark:text-neutral-400"
                    >
                        Descrição
                    </h4>
                    <p class="mt-1 text-sm">
                        {{ selectedExercise.description || 'Sem descrição' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4
                            class="text-sm font-semibold text-neutral-500 dark:text-neutral-400"
                        >
                            Categoria
                        </h4>
                        <p class="mt-1 text-sm">
                            {{
                                selectedExercise.category?.name ||
                                selectedExercise.muscle_group
                            }}
                        </p>
                    </div>
                    <div>
                        <h4
                            class="text-sm font-semibold text-neutral-500 dark:text-neutral-400"
                        >
                            Equipamento
                        </h4>
                        <p class="mt-1 text-sm">
                            {{ selectedExercise.equipment || 'Não informado' }}
                        </p>
                    </div>
                    <div>
                        <h4
                            class="text-sm font-semibold text-neutral-500 dark:text-neutral-400"
                        >
                            Dificuldade
                        </h4>
                        <p class="mt-1 text-sm">
                            <span
                                :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium',
                                    getDifficultyColor(
                                        selectedExercise.difficulty,
                                    ),
                                ]"
                            >
                                {{
                                    getDifficultyLabel(
                                        selectedExercise.difficulty,
                                    )
                                }}
                            </span>
                        </p>
                    </div>
                </div>

                <div v-if="selectedExercise.instructions">
                    <h4
                        class="text-sm font-semibold text-neutral-500 dark:text-neutral-400"
                    >
                        Instruções
                    </h4>
                    <p class="mt-1 text-sm whitespace-pre-line">
                        {{ selectedExercise.instructions }}
                    </p>
                </div>

                <div v-if="selectedExercise.video_url">
                    <h4
                        class="text-sm font-semibold text-neutral-500 dark:text-neutral-400"
                    >
                        Vídeo
                    </h4>
                    <a
                        :href="selectedExercise.video_url"
                        target="_blank"
                        class="mt-1 text-sm text-emerald-600 hover:underline"
                    >
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
</template>
