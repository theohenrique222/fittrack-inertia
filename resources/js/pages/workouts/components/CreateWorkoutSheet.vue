<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    Activity,
    ChevronDown,
    Clock,
    Dumbbell,
    Plus,
    Sparkles,
    Trash2,
    User,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { generate, store } from '@/routes/workouts';

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

const props = defineProps<{
    students: Student[];
    exercises: Exercise[];
    categories: Category[];
    preSelectedClientId?: string;
}>();

const emit = defineEmits(['close']);

const mode = ref<'manual' | 'auto'>('auto');

interface WorkoutExercise {
    exercise_id: number;
    sets: number;
    reps: number;
    rest_seconds: number;
    order: number;
    notes: string;
}

const workoutExercises = ref<WorkoutExercise[]>([]);
const selectedCategoryIds = ref<number[]>([]);

const form = useForm({
    name: '',
    description: '',
    client_id: props.preSelectedClientId || '',
    exercises: [] as WorkoutExercise[],
    category_ids: [] as number[],
    is_active: true,
});

const selectedStudentName = computed(() => {
    if (!form.client_id) return '';
    const student = props.students.find(
        (s) => String(s.id) === form.client_id,
    );
    return student ? student.name : '';
});

watch(
    () => props.preSelectedClientId,
    (newVal) => {
        if (newVal) {
            form.client_id = newVal;
        }
    },
);

function toggleCategory(categoryId: number) {
    const index = selectedCategoryIds.value.indexOf(categoryId);

    if (index === -1) {
        selectedCategoryIds.value.push(categoryId);
    } else {
        selectedCategoryIds.value.splice(index, 1);
    }
}

function addExercise() {
    workoutExercises.value.push({
        exercise_id: 0,
        sets: 3,
        reps: 10,
        rest_seconds: 60,
        order: workoutExercises.value.length,
        notes: '',
    });
}

function removeExercise(index: number) {
    workoutExercises.value.splice(index, 1);
    workoutExercises.value.forEach((ex, i) => {
        ex.order = i;
    });
}

function handleSubmit() {
    if (mode.value === 'auto') {
        if (selectedCategoryIds.value.length === 0) {
            return;
        }

        form.category_ids = selectedCategoryIds.value;

        form.post(generate.url(), {
            onSuccess: () => {
                form.reset();
                selectedCategoryIds.value = [];
                emit('close');
            },
        });
    } else {
        form.exercises = workoutExercises.value.filter(
            (e) => e.exercise_id > 0,
        );

        if (form.exercises.length === 0) {
            return;
        }

        form.post(store.url(), {
            onSuccess: () => {
                form.reset();
                workoutExercises.value = [];
                emit('close');
            },
        });
    }
}

function handleCancel() {
    form.reset();
    workoutExercises.value = [];
    selectedCategoryIds.value = [];
    emit('close');
}
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Informações do Treino
                </h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                    Preencha os dados básicos do treino
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <User class="w-3.5 h-3.5 text-neutral-400" />
                        Aluno *
                    </Label>
                    <Select v-model="form.client_id">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione o aluno..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="student in students"
                                :key="student.id"
                                :value="String(student.id)"
                            >
                                {{ student.name
                                }}{{ student.nickname ? ` (${student.nickname})` : '' }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span
                        v-if="form.errors.client_id"
                        class="text-xs text-red-500 mt-1"
                        >{{ form.errors.client_id }}</span
                    >
                </div>

                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <Activity class="w-3.5 h-3.5 text-neutral-400" />
                        Nome do Treino
                    </Label>
                    <Input
                        v-model="form.name"
                        type="text"
                        placeholder="Ex: Treino A - Peito e Tríceps"
                    />
                    <span
                        v-if="form.errors.name"
                        class="text-xs text-red-500 mt-1"
                        >{{ form.errors.name }}</span
                    >
                </div>

                <div>
                    <Label class="mb-2">Descrição</Label>
                    <Textarea
                        v-model="form.description"
                        placeholder="Observações gerais sobre o treino..."
                        rows="2"
                    />
                    <span
                        v-if="form.errors.description"
                        class="text-xs text-red-500 mt-1"
                        >{{ form.errors.description }}</span
                    >
                </div>
            </div>

            <div class="border-t border-neutral-200 dark:border-neutral-700 pt-6">
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Modo de Criação
                    </h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                        Escolha como deseja montar o treino
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <button
                        type="button"
                        @click="mode = 'auto'"
                        :class="[
                            'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all',
                            mode === 'auto'
                                ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20'
                                : 'border-neutral-200 dark:border-neutral-700 hover:border-neutral-300 dark:hover:border-neutral-600',
                        ]"
                    >
                        <div
                            :class="[
                                'flex h-10 w-10 items-center justify-center rounded-lg',
                                mode === 'auto'
                                    ? 'bg-emerald-500 text-white'
                                    : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500',
                            ]"
                        >
                            <Sparkles class="w-5 h-5" />
                        </div>
                        <span
                            :class="[
                                'text-sm font-medium',
                                mode === 'auto'
                                    ? 'text-emerald-700 dark:text-emerald-300'
                                    : 'text-neutral-700 dark:text-neutral-300',
                            ]"
                        >
                            Automático
                        </span>
                    </button>

                    <button
                        type="button"
                        @click="mode = 'manual'"
                        :class="[
                            'flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all',
                            mode === 'manual'
                                ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20'
                                : 'border-neutral-200 dark:border-neutral-700 hover:border-neutral-300 dark:hover:border-neutral-600',
                        ]"
                    >
                        <div
                            :class="[
                                'flex h-10 w-10 items-center justify-center rounded-lg',
                                mode === 'manual'
                                    ? 'bg-emerald-500 text-white'
                                    : 'bg-neutral-100 dark:bg-neutral-800 text-neutral-500',
                            ]"
                        >
                            <Dumbbell class="w-5 h-5" />
                        </div>
                        <span
                            :class="[
                                'text-sm font-medium',
                                mode === 'manual'
                                    ? 'text-emerald-700 dark:text-emerald-300'
                                    : 'text-neutral-700 dark:text-neutral-300',
                            ]"
                        >
                            Manual
                        </span>
                    </button>
                </div>
            </div>

            <div
                v-if="mode === 'auto'"
                class="border-t border-neutral-200 dark:border-neutral-700 pt-6"
            >
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Grupos Musculares
                    </h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                        Selecione os grupos para gerar o treino automaticamente
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 mt-4">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        type="button"
                        @click="toggleCategory(category.id)"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium border transition-all',
                            selectedCategoryIds.includes(category.id)
                                ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm'
                                : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:border-emerald-300 dark:hover:border-emerald-600',
                        ]"
                    >
                        {{ category.name }}
                    </button>
                </div>

                <div
                    v-if="selectedCategoryIds.length > 0"
                    class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20"
                >
                    <Sparkles class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">
                        {{ selectedCategoryIds.length }} grupo(s) selecionado(s)
                    </span>
                </div>

                <span
                    v-if="form.errors.category_ids"
                    class="text-xs text-red-500 mt-2 block"
                    >{{ form.errors.category_ids }}</span
                >
            </div>

            <div
                v-if="mode === 'manual'"
                class="border-t border-neutral-200 dark:border-neutral-700 pt-6"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                            Exercícios
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                            Adicione os exercícios do treino
                        </p>
                    </div>

                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="gap-1.5"
                        @click="addExercise"
                    >
                        <Plus class="w-4 h-4" />
                        Adicionar
                    </Button>
                </div>

                <div
                    v-if="workoutExercises.length === 0"
                    class="flex flex-col items-center justify-center py-8 text-center"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800 mb-3"
                    >
                        <Dumbbell class="h-6 w-6 text-neutral-400" />
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Nenhum exercício adicionado
                    </p>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">
                        Clique em "Adicionar" para começar
                    </p>
                </div>

                <div v-else class="space-y-3 mt-4">
                    <div
                        v-for="(exercise, index) in workoutExercises"
                        :key="index"
                        class="rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 overflow-hidden"
                    >
                        <div
                            class="flex items-center justify-between px-4 py-3 bg-neutral-50 dark:bg-neutral-900/50 border-b border-neutral-200 dark:border-neutral-700"
                        >
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-xs font-semibold text-emerald-700 dark:text-emerald-300"
                                >
                                    {{ index + 1 }}
                                </div>
                                <span class="text-sm font-medium text-neutral-900 dark:text-white">
                                    Exercício
                                </span>
                            </div>

                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                class="h-7 w-7 p-0 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                @click="removeExercise(index)"
                            >
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>

                        <div class="p-4 space-y-3">
                            <div>
                                <Select
                                    :model-value="String(exercise.exercise_id)"
                                    @update:model-value="
                                        exercise.exercise_id = Number($event)
                                    "
                                >
                                    <SelectTrigger>
                                        <SelectValue
                                            placeholder="Selecione o exercício..."
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="ex in exercises"
                                            :key="ex.id"
                                            :value="String(ex.id)"
                                        >
                                            {{ ex.name }}
                                            <span
                                                v-if="ex.category"
                                                class="text-xs text-neutral-500"
                                            >
                                                ({{ ex.category.name }})
                                            </span>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <Label class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5 flex items-center gap-1">
                                        <ChevronDown class="w-3 h-3" />
                                        Séries
                                    </Label>
                                    <Input
                                        v-model.number="exercise.sets"
                                        type="number"
                                        min="1"
                                        class="h-9"
                                    />
                                </div>

                                <div>
                                    <Label class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                        Repetições
                                    </Label>
                                    <Input
                                        v-model.number="exercise.reps"
                                        type="number"
                                        min="1"
                                        class="h-9"
                                    />
                                </div>

                                <div>
                                    <Label class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5 flex items-center gap-1">
                                        <Clock class="w-3 h-3" />
                                        Descanso (s)
                                    </Label>
                                    <Input
                                        v-model.number="exercise.rest_seconds"
                                        type="number"
                                        min="0"
                                        class="h-9"
                                    />
                                </div>
                            </div>

                            <div>
                                <Label class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                    Observações
                                </Label>
                                <Input
                                    v-model="exercise.notes"
                                    type="text"
                                    class="h-9"
                                    placeholder="Opcional"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <span
                    v-if="form.errors.exercises"
                    class="text-xs text-red-500 mt-2 block"
                    >{{ form.errors.exercises }}</span
                >
            </div>
        </div>

        <div
            class="sticky bottom-0 px-6 py-4 bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-700"
        >
            <div class="flex gap-3">
                <Button
                    type="button"
                    variant="outline"
                    class="flex-1 cursor-pointer"
                    @click="handleCancel"
                    :disabled="form.processing"
                >
                    Cancelar
                </Button>

                <Button
                    type="submit"
                    class="flex-1 cursor-pointer bg-emerald-600 hover:bg-emerald-700"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? 'Salvando...'
                            : mode === 'auto'
                              ? 'Gerar Treino'
                              : 'Salvar Treino'
                    }}
                </Button>
            </div>
        </div>
    </form>
</template>
