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
    ChevronDown,
    Dumbbell,
    Pencil,
    Play,
    Plus,
    Search,
    Trash2,
    Trophy,
    Zap,
    Footprints,
    Activity,
    Target,
    MoveHorizontal,
    X,
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
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
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
    exercises?: Exercise[];
    categories?: Category[];
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const searchQuery = ref('');
const selectedDifficulty = ref<string>('all');
const selectedCategory = ref<string>('all');
const collapsedGroups = ref<Set<string>>(new Set());

const categoriesList = computed(() => {
    const seen = new Map<string, Category>();

    for (const e of props.exercises || []) {
        if (e.category && !seen.has(e.category.slug)) {
            seen.set(e.category.slug, e.category);
        }
    }

    return Array.from(seen.values()).sort((a, b) => a.name.localeCompare(b.name));
});

const filteredExercises = computed(() => {
    let result = props.exercises || [];

    if (selectedDifficulty.value !== 'all') {
        result = result.filter(
            (e) => e.difficulty === selectedDifficulty.value,
        );
    }

    if (selectedCategory.value !== 'all') {
        result = result.filter(
            (e) => e.category?.slug === selectedCategory.value,
        );
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (e) =>
                e.name.toLowerCase().includes(query) ||
                e.description?.toLowerCase().includes(query) ||
                e.category?.name.toLowerCase().includes(query) ||
                e.muscle_group?.toLowerCase().includes(query) ||
                e.equipment?.toLowerCase().includes(query),
        );
    }

    return result;
});

const stats = computed(() => {
    const exercises = props.exercises || [];
    const uniqueMuscleGroups = new Set(exercises.map((e) => e.muscle_group));

    return {
        total: exercises.length,
        active: exercises.filter((e) => e.is_active).length,
        muscleGroups: uniqueMuscleGroups.size,
    };
});

const groupedExercises = computed(() => {
    const exercises = filteredExercises.value;
    const groups: Record<string, { category: Category | null; exercises: Exercise[] }> = {};

    const categoryOrder = ['chest', 'back', 'shoulders', 'biceps', 'triceps', 'forearms', 'abs', 'quadriceps', 'hamstrings', 'glutes', 'calves', 'full-body'];

    for (const exercise of exercises) {
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

const totalFiltered = computed(() => filteredExercises.value.length);
const hasActiveFilters = computed(
    () => searchQuery.value || selectedDifficulty.value !== 'all' || selectedCategory.value !== 'all',
);

const activeFiltersCount = computed(() => {
    let count = 0;

    if (selectedDifficulty.value !== 'all') {
count++;
}

    if (selectedCategory.value !== 'all') {
count++;
}

    if (searchQuery.value) {
count++;
}

    return count;
});

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

const clearFilters = () => {
    searchQuery.value = '';
    selectedDifficulty.value = 'all';
    selectedCategory.value = 'all';
};

const toggleGroup = (key: string) => {
    if (collapsedGroups.value.has(key)) {
        collapsedGroups.value.delete(key);
    } else {
        collapsedGroups.value.add(key);
    }
};

const isGroupCollapsed = (key: string) => !collapsedGroups.value.has(key);

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

function getDifficultyDot(difficulty: string): string {
    switch (difficulty) {
        case 'Beginner':
            return 'bg-blue-500';
        case 'Intermediate':
            return 'bg-yellow-500';
        case 'Advanced':
            return 'bg-red-500';
        default:
            return 'bg-neutral-400';
    }
}

const avatarColors = [
    'from-emerald-400 to-emerald-600',
    'from-teal-400 to-teal-600',
    'from-cyan-400 to-cyan-600',
    'from-green-400 to-green-600',
    'from-lime-400 to-lime-600',
];

const muscleGroupColors: Record<string, { bg: string; text: string; border: string; badge: string; icon: string; cardAccent: string }> = {
    chest: {
        bg: 'bg-red-50 dark:bg-red-950/30',
        text: 'text-red-700 dark:text-red-300',
        border: 'border-red-200 dark:border-red-800',
        badge: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        icon: 'text-red-500',
        cardAccent: 'via-red-500',
    },
    back: {
        bg: 'bg-blue-50 dark:bg-blue-950/30',
        text: 'text-blue-700 dark:text-blue-300',
        border: 'border-blue-200 dark:border-blue-800',
        badge: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
        icon: 'text-blue-500',
        cardAccent: 'via-blue-500',
    },
    shoulders: {
        bg: 'bg-yellow-50 dark:bg-yellow-950/30',
        text: 'text-yellow-700 dark:text-yellow-300',
        border: 'border-yellow-200 dark:border-yellow-800',
        badge: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
        icon: 'text-yellow-500',
        cardAccent: 'via-yellow-500',
    },
    biceps: {
        bg: 'bg-purple-50 dark:bg-purple-950/30',
        text: 'text-purple-700 dark:text-purple-300',
        border: 'border-purple-200 dark:border-purple-800',
        badge: 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
        icon: 'text-purple-500',
        cardAccent: 'via-purple-500',
    },
    triceps: {
        bg: 'bg-pink-50 dark:bg-pink-950/30',
        text: 'text-pink-700 dark:text-pink-300',
        border: 'border-pink-200 dark:border-pink-800',
        badge: 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300',
        icon: 'text-pink-500',
        cardAccent: 'via-pink-500',
    },
    forearms: {
        bg: 'bg-indigo-50 dark:bg-indigo-950/30',
        text: 'text-indigo-700 dark:text-indigo-300',
        border: 'border-indigo-200 dark:border-indigo-800',
        badge: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
        icon: 'text-indigo-500',
        cardAccent: 'via-indigo-500',
    },
    abs: {
        bg: 'bg-emerald-50 dark:bg-emerald-950/30',
        text: 'text-emerald-700 dark:text-emerald-300',
        border: 'border-emerald-200 dark:border-emerald-800',
        badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        icon: 'text-emerald-500',
        cardAccent: 'via-emerald-500',
    },
    quadriceps: {
        bg: 'bg-orange-50 dark:bg-orange-950/30',
        text: 'text-orange-700 dark:text-orange-300',
        border: 'border-orange-200 dark:border-orange-800',
        badge: 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
        icon: 'text-orange-500',
        cardAccent: 'via-orange-500',
    },
    hamstrings: {
        bg: 'bg-amber-50 dark:bg-amber-950/30',
        text: 'text-amber-700 dark:text-amber-300',
        border: 'border-amber-200 dark:border-amber-800',
        badge: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        icon: 'text-amber-500',
        cardAccent: 'via-amber-500',
    },
    glutes: {
        bg: 'bg-rose-50 dark:bg-rose-950/30',
        text: 'text-rose-700 dark:text-rose-300',
        border: 'border-rose-200 dark:border-rose-800',
        badge: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
        icon: 'text-rose-500',
        cardAccent: 'via-rose-500',
    },
    calves: {
        bg: 'bg-teal-50 dark:bg-teal-950/30',
        text: 'text-teal-700 dark:text-teal-300',
        border: 'border-teal-200 dark:border-teal-800',
        badge: 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300',
        icon: 'text-teal-500',
        cardAccent: 'via-teal-500',
    },
    'full-body': {
        bg: 'bg-cyan-50 dark:bg-cyan-950/30',
        text: 'text-cyan-700 dark:text-cyan-300',
        border: 'border-cyan-200 dark:border-cyan-800',
        badge: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300',
        icon: 'text-cyan-500',
        cardAccent: 'via-cyan-500',
    },
};

const muscleGroupIcons: Record<string, any> = {
    chest: Target,
    back: MoveHorizontal,
    shoulders: Zap,
    biceps: MoveHorizontal,
    triceps: MoveHorizontal,
    forearms: MoveHorizontal,
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

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white"
        >
            <div
                class="pointer-events-none absolute -inset-[100%] animate-[shimmer_3s_ease-in-out_infinite] bg-[repeating-linear-gradient(90deg,transparent,transparent_40%,rgba(255,255,255,0.06)_50%,transparent_60%,transparent_100%)] bg-[length:200%_100%]"
            />

            <div class="relative z-10 mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Exercícios</h1>
                    <p class="mt-1 text-sm text-emerald-100/80">
                        Gerencie o catálogo de exercícios do sistema
                    </p>
                </div>

                <Dialog v-model:open="isCreateOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="inline-flex items-center gap-2 border-0 bg-white text-emerald-700 shadow-lg transition-all hover:bg-emerald-50 hover:shadow-xl active:scale-95"
                        >
                            <Plus class="h-4 w-4" />
                            Novo Exercício
                        </Button>
                    </DialogTrigger>

                    <DialogContent class="max-h-[90vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Criar Exercício</DialogTitle>
                        </DialogHeader>

                        <CreateExerciseSheet
                            :categories="categories"
                            @close="closeCreateSheet"
                        />
                    </DialogContent>
                </Dialog>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-3">
                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Dumbbell class="h-4 w-4 text-emerald-100/70" />
                        <span class="text-xs font-medium text-emerald-100/70">Total</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.total }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Zap class="h-4 w-4 text-emerald-100/70" />
                        <span class="text-xs font-medium text-emerald-100/70">Ativos</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.active }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Trophy class="h-4 w-4 text-emerald-100/70" />
                        <span class="text-xs font-medium text-emerald-100/70">Grupos</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.muscleGroups }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 px-6 py-5 dark:bg-neutral-900">
            <div class="mb-5 space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-neutral-400"
                        />
                        <Input
                            v-model="searchQuery"
                            placeholder="Buscar exercício por nome, categoria ou equipamento..."
                            class="border-neutral-200 bg-white pl-9 transition-all focus:border-emerald-400 focus:ring-emerald-400 dark:border-neutral-700 dark:bg-neutral-800"
                        />
                        <button
                            v-if="searchQuery"
                            class="absolute top-1/2 right-3 -translate-y-1/2 text-neutral-400 transition-colors hover:text-neutral-600"
                            @click="searchQuery = ''"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <div class="w-full sm:w-48">
                        <select
                            v-model="selectedDifficulty"
                            class="h-10 w-full cursor-pointer rounded-lg border border-neutral-200 bg-white px-3 text-sm transition-all focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="all">Todas dificuldades</option>
                            <option value="Beginner">Iniciante</option>
                            <option value="Intermediate">Intermediário</option>
                            <option value="Advanced">Avançado</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        :class="[
                            'rounded-full px-3.5 py-1.5 text-xs font-medium transition-all',
                            selectedCategory === 'all'
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'border border-neutral-200 bg-white text-neutral-600 hover:border-emerald-300 hover:text-emerald-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-emerald-600 dark:hover:text-emerald-300',
                        ]"
                        @click="selectedCategory = 'all'"
                    >
                        Todas
                    </button>
                    <button
                        v-for="cat in categoriesList"
                        :key="cat.slug"
                        :class="[
                            'rounded-full px-3.5 py-1.5 text-xs font-medium transition-all',
                            selectedCategory === cat.slug
                                ? 'bg-emerald-600 text-white shadow-sm'
                                : 'border border-neutral-200 bg-white text-neutral-600 hover:border-emerald-300 hover:text-emerald-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-emerald-600 dark:hover:text-emerald-300',
                        ]"
                        @click="selectedCategory = cat.slug"
                    >
                        {{ cat.name }}
                    </button>

                    <div v-if="hasActiveFilters" class="ml-auto">
                        <button
                            class="inline-flex items-center gap-1 rounded-full px-3 py-1.5 text-xs font-medium text-neutral-500 transition-colors hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                            @click="clearFilters"
                        >
                            <X class="h-3 w-3" />
                            Limpar filtros
                            <span
                                class="inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-neutral-200 px-1 text-[10px] font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300"
                            >
                                {{ activeFiltersCount }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="hasActiveFilters && !totalFiltered" class="flex flex-col items-center justify-center py-16 text-center">
                <div
                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800"
                >
                    <Search class="h-8 w-8 text-neutral-300 dark:text-neutral-600" />
                </div>
                <p class="font-medium text-neutral-600 dark:text-neutral-400">
                    Nenhum exercício encontrado
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    Tente ajustar os filtros ou buscar com outros termos
                </p>
                <button
                    class="mt-4 inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition-all hover:border-emerald-300 hover:text-emerald-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:border-emerald-600 dark:hover:text-emerald-300"
                    @click="clearFilters"
                >
                    <X class="h-3.5 w-3.5" />
                    Limpar filtros
                </button>
            </div>

            <div v-else-if="!groupedExercises.length && !hasActiveFilters" class="flex flex-col items-center justify-center py-16 text-center">
                <div
                    class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40"
                >
                    <Dumbbell class="h-10 w-10 text-emerald-400 dark:text-emerald-500" />
                </div>
                <p class="text-lg font-semibold text-neutral-700 dark:text-neutral-300">
                    Nenhum exercício cadastrado
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    Clique em "Novo Exercício" para começar a construir seu catálogo
                </p>
            </div>

            <div v-else class="space-y-6">
                <div
                    v-for="group in groupedExercises"
                    :key="group.key"
                    class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <button
                        class="flex w-full items-center gap-3 px-5 py-4 text-left transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-750"
                        @click="toggleGroup(group.key)"
                    >
                        <component
                            :is="getMuscleGroupIcon(group.category?.slug)"
                            :class="[
                                'h-5 w-5 shrink-0',
                                getMuscleGroupColor(group.category?.slug).icon,
                            ]"
                        />
                        <h2
                            :class="[
                                'text-base font-semibold',
                                getMuscleGroupColor(group.category?.slug).text,
                            ]"
                        >
                            {{ group.category?.name || 'Sem Categoria' }}
                        </h2>
                        <span
                            :class="[
                                'rounded-full px-2.5 py-0.5 text-xs font-medium',
                                getMuscleGroupColor(group.category?.slug).badge,
                            ]"
                        >
                            {{ group.exercises.length }}
                        </span>
                        <ChevronDown
                            class="ml-auto h-4 w-4 text-neutral-400 transition-transform duration-200 dark:text-neutral-500"
                            :class="{ 'rotate-180': isGroupCollapsed(group.key) }"
                        />
                    </button>

                    <div
                        v-show="!isGroupCollapsed(group.key)"
                        class="border-t border-neutral-100 p-4 dark:border-neutral-700"
                    >
                        <div
                            class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                v-for="exercise in group.exercises"
                                :key="exercise.id"
                                class="group/card relative cursor-pointer overflow-hidden rounded-xl border border-neutral-100 bg-neutral-50 transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800/50 dark:hover:border-emerald-700"
                                @click="handleViewClick(exercise)"
                            >
                                <div
                                    class="absolute top-0 left-0 h-full w-1 rounded-l-xl bg-gradient-to-b"
                                    :class="getMuscleGroupColor(group.category?.slug).cardAccent || 'from-emerald-500 to-emerald-600'"
                                />

                                <div class="flex items-start gap-3 p-4 pl-5">
                                    <div
                                        :class="[
                                            'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br font-semibold text-white shadow-sm',
                                            getAvatarColor(exercise.id),
                                        ]"
                                    >
                                        <Dumbbell class="h-4 w-4" />
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <h3
                                                class="truncate text-sm font-semibold text-neutral-900 dark:text-white"
                                            >
                                                {{ exercise.name }}
                                            </h3>
                                            <span
                                                class="mt-0.5 inline-flex shrink-0 items-center gap-1.5 rounded-full px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide"
                                                :class="getDifficultyColor(exercise.difficulty)"
                                            >
                                                <span
                                                    class="inline-block h-1.5 w-1.5 rounded-full"
                                                    :class="getDifficultyDot(exercise.difficulty)"
                                                />
                                                {{ getDifficultyLabel(exercise.difficulty) }}
                                            </span>
                                        </div>

                                        <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1">
                                            <span
                                                v-if="exercise.equipment"
                                                class="inline-flex items-center gap-1 text-[11px] text-neutral-500 dark:text-neutral-400"
                                            >
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M6 20h0a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z" />
                                                    <path d="M18 20h0a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z" />
                                                    <path d="M6 14h12" />
                                                    <path d="M6 10h12" />
                                                </svg>
                                                {{ exercise.equipment }}
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-1 text-[11px] capitalize text-neutral-500 dark:text-neutral-400"
                                            >
                                                <component
                                                    :is="getMuscleGroupIcon(group.category?.slug)"
                                                    class="h-3 w-3"
                                                />
                                                {{ exercise.muscle_group }}
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="shrink-0 opacity-0 transition-all group-hover/card:opacity-100"
                                    >
                                        <ChevronRight
                                            class="h-4 w-4 text-emerald-400"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="absolute inset-0 flex items-center justify-center rounded-xl bg-black/0 transition-colors group-hover/card:bg-black/[0.02] dark:group-hover/card:bg-white/[0.02]"
                                >
                                    <div
                                        class="absolute top-2 right-2 flex gap-1 opacity-0 transition-all group-hover/card:opacity-100"
                                        @click.stop
                                    >
                                        <button
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-neutral-200 bg-white text-neutral-500 shadow-sm transition-all hover:border-emerald-300 hover:text-emerald-600 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-400 dark:hover:border-emerald-500 dark:hover:text-emerald-300"
                                            @click.stop="handleEditClick(exercise)"
                                            title="Editar"
                                        >
                                            <Pencil class="h-3.5 w-3.5" />
                                        </button>
                                        <button
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-neutral-200 bg-white text-red-500 shadow-sm transition-all hover:border-red-300 hover:bg-red-50 dark:border-neutral-600 dark:bg-neutral-700 dark:text-red-400 dark:hover:border-red-500 dark:hover:bg-red-950/30"
                                            @click.stop="handleDeleteClick(exercise.id)"
                                            title="Excluir"
                                        >
                                            <Trash2 class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Sheet v-model:open="isViewOpen">
        <SheetContent class="w-full overflow-y-auto sm:max-w-lg">
            <SheetHeader class="border-b border-neutral-100 px-6 pt-6 pb-4 dark:border-neutral-700">
                <SheetTitle class="flex items-center gap-2 text-xl">
                    <div
                        :class="[
                            'flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br font-semibold text-white text-xs shadow-sm',
                            selectedExercise ? getAvatarColor(selectedExercise.id) : '',
                        ]"
                    >
                        <Dumbbell class="h-4 w-4" />
                    </div>
                    {{ selectedExercise?.name }}
                </SheetTitle>
            </SheetHeader>

            <div v-if="selectedExercise" class="space-y-5 px-6 pt-5 pb-6">
                <div
                    class="h-52 w-full overflow-hidden rounded-xl bg-neutral-100 shadow-inner dark:bg-neutral-700"
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

                <div
                    v-if="selectedExercise.description"
                    class="rounded-lg border border-neutral-100 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <h4
                        class="mb-1.5 text-[11px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400"
                    >
                        Descrição
                    </h4>
                    <p class="text-sm leading-relaxed text-neutral-700 dark:text-neutral-300">
                        {{ selectedExercise.description }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-neutral-100 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                        <h4
                            class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400"
                        >
                            Categoria
                        </h4>
                        <p class="text-sm font-medium capitalize text-neutral-900 dark:text-white">
                            {{ selectedExercise.category?.name || selectedExercise.muscle_group }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-neutral-100 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                        <h4
                            class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400"
                        >
                            Equipamento
                        </h4>
                        <p class="text-sm font-medium text-neutral-900 dark:text-white">
                            {{ selectedExercise.equipment || 'Não informado' }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-neutral-100 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                        <h4
                            class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400"
                        >
                            Dificuldade
                        </h4>
                        <p>
                            <span
                                :class="[
                                    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium',
                                    getDifficultyColor(selectedExercise.difficulty),
                                ]"
                            >
                                <span
                                    class="inline-block h-1.5 w-1.5 rounded-full"
                                    :class="getDifficultyDot(selectedExercise.difficulty)"
                                />
                                {{ getDifficultyLabel(selectedExercise.difficulty) }}
                            </span>
                        </p>
                    </div>
                    <div class="rounded-lg border border-neutral-100 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                        <h4
                            class="mb-1 text-[10px] font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400"
                        >
                            Grupo Muscular
                        </h4>
                        <p class="text-sm font-medium capitalize text-neutral-900 dark:text-white">
                            {{ selectedExercise.muscle_group }}
                        </p>
                    </div>
                </div>

                <div
                    v-if="selectedExercise.instructions"
                    class="rounded-lg border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-4 dark:border-emerald-800 dark:from-emerald-950/20 dark:to-neutral-800"
                >
                    <h4
                        class="mb-1.5 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-emerald-700 dark:text-emerald-300"
                    >
                        <Activity class="h-3.5 w-3.5" />
                        Instruções
                    </h4>
                    <p class="text-sm leading-relaxed whitespace-pre-line text-emerald-900 dark:text-emerald-100">
                        {{ selectedExercise.instructions }}
                    </p>
                </div>

                <div
                    v-if="selectedExercise.video_url"
                    class="flex items-center justify-between rounded-lg border border-neutral-100 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <div>
                        <h4 class="flex items-center gap-1.5 text-sm font-semibold text-neutral-900 dark:text-white">
                            <Play class="h-4 w-4 text-emerald-500" />
                            Vídeo Demonstrativo
                        </h4>
                        <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">
                            Assista a demonstração completa
                        </p>
                    </div>
                    <a
                        :href="selectedExercise.video_url"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-emerald-700 hover:shadow-md active:scale-95"
                    >
                        <Play class="h-4 w-4 fill-white" />
                        Assistir
                    </a>
                </div>

                <div class="flex gap-2 pt-2">
                    <button
                        @click="handleEditClick(selectedExercise)"
                        class="flex flex-1 items-center justify-center gap-2 rounded-lg border border-neutral-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-all hover:border-emerald-300 hover:text-emerald-700 hover:shadow-md active:scale-[0.98] dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:border-emerald-600 dark:hover:text-emerald-300"
                    >
                        <Pencil class="h-4 w-4" />
                        Editar Exercício
                    </button>
                    <button
                        @click="handleDeleteClick(selectedExercise.id)"
                        class="flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 shadow-sm transition-all hover:border-red-300 hover:bg-red-50 hover:shadow-md active:scale-[0.98] dark:border-red-800 dark:bg-neutral-800 dark:text-red-400 dark:hover:border-red-600 dark:hover:bg-red-950/30"
                    >
                        <Trash2 class="h-4 w-4" />
                        Excluir
                    </button>
                </div>
            </div>
        </SheetContent>
    </Sheet>

    <Dialog v-model:open="isEditOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Exercício</DialogTitle>
            </DialogHeader>

            <EditExerciseSheet
                v-if="selectedExercise"
                :exercise="selectedExercise"
                :categories="categories"
                @close="closeEditSheet"
            />
        </DialogContent>
    </Dialog>
</template>

<style scoped>
@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
</style>
