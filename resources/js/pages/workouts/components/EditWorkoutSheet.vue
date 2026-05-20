<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ChevronDown, Clock, Dumbbell, Minus, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
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
import { update } from '@/routes/workouts';

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

interface WorkoutExercise {
    exercise_id: number;
    sets: number;
    reps: number;
    rest_seconds: number;
    order: number;
    notes: string;
}

interface Workout {
    id: number;
    name: string;
    description?: string;
    student_id?: number;
    client_id?: number;
    is_active: boolean;
    exercises?: {
        id: number;
        name: string;
        pivot: {
            sets: number;
            reps: number;
            rest_seconds: number;
            order: number;
            notes?: string;
        };
    }[];
}

interface Props {
    workout: Workout;
    student: Student;
    exercises: Exercise[];
}

const props = defineProps<Props>();

const emit = defineEmits(['close']);

const workoutExercises = ref<WorkoutExercise[]>([]);

watch(
    () => props.workout,
    (workout) => {
        workoutExercises.value = workout.exercises?.map((ex) => ({
            exercise_id: ex.id,
            sets: ex.pivot.sets,
            reps: ex.pivot.reps,
            rest_seconds: ex.pivot.rest_seconds,
            order: ex.pivot.order,
            notes: ex.pivot.notes ?? '',
        })) ?? [];
    },
    { immediate: true },
);

const form = useForm({
    name: props.workout.name,
    description: props.workout.description || '',
    client_id: props.workout.client_id || props.workout.student_id || 0,
    exercises: [] as WorkoutExercise[],
    is_active: props.workout.is_active,
});

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
    form.exercises = workoutExercises.value.filter((e) => e.exercise_id > 0);

    if (form.exercises.length === 0) {
        return;
    }

    form.post(update.url(props.workout.id), {
        method: 'put',
        onSuccess: () => {
            emit('close');
        },
    });
}

function handleCancel() {
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
                    Edite os dados básicos do treino
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <Dumbbell class="w-3.5 h-3.5 text-neutral-400" />
                        Nome do Treino *
                    </Label>
                    <Input
                        v-model="form.name"
                        type="text"
                        placeholder="Ex: Treino A - Peito e Tríceps"
                    />
                    <span
                        v-if="form.errors.name"
                        class="text-xs text-red-500 mt-1"
                    >{{ form.errors.name }}</span>
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
                    >{{ form.errors.description }}</span>
                </div>

                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <Clock class="w-3.5 h-3.5 text-neutral-400" />
                        Aluno
                    </Label>
                    <div class="h-10 px-3 py-2 rounded-md border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-sm text-neutral-700 dark:text-neutral-300">
                        {{ props.student.name }}{{ props.student.nickname ? ` (${props.student.nickname})` : '' }}
                    </div>
                </div>
            </div>

            <div class="border-t border-neutral-200 dark:border-neutral-700 pt-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                            Exercícios
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                            Adicione ou remova exercícios do treino
                        </p>
                    </div>

                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        class="gap-1.5"
                        @click="addExercise"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
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
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="exercise.sets = Math.max(1, exercise.sets - 1)"
                                            :disabled="exercise.sets <= 1"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-neutral-200 bg-white text-neutral-600 transition-all hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-neutral-200 disabled:hover:text-neutral-600 disabled:hover:bg-white dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-emerald-600 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20 dark:disabled:hover:border-neutral-700 dark:disabled:hover:text-neutral-400 dark:disabled:hover:bg-neutral-800"
                                        >
                                            <Minus class="w-4 h-4" />
                                        </button>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-center rounded-lg border-2 border-neutral-200 bg-neutral-50 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900/50">
                                                <span class="text-xl font-bold text-neutral-900 dark:text-white">{{ exercise.sets }}</span>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            @click="exercise.sets = exercise.sets + 1"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-neutral-200 bg-white text-neutral-600 transition-all hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-emerald-600 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20"
                                        >
                                            <Plus class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <Label class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5">
                                        Repetições
                                    </Label>
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="exercise.reps = Math.max(1, exercise.reps - 1)"
                                            :disabled="exercise.reps <= 1"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-neutral-200 bg-white text-neutral-600 transition-all hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-neutral-200 disabled:hover:text-neutral-600 disabled:hover:bg-white dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-blue-600 dark:hover:text-blue-400 dark:hover:bg-blue-900/20 dark:disabled:hover:border-neutral-700 dark:disabled:hover:text-neutral-400 dark:disabled:hover:bg-neutral-800"
                                        >
                                            <Minus class="w-4 h-4" />
                                        </button>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-center rounded-lg border-2 border-neutral-200 bg-neutral-50 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900/50">
                                                <span class="text-xl font-bold text-neutral-900 dark:text-white">{{ exercise.reps }}</span>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            @click="exercise.reps = exercise.reps + 1"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-neutral-200 bg-white text-neutral-600 transition-all hover:border-blue-300 hover:text-blue-600 hover:bg-blue-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-blue-600 dark:hover:text-blue-400 dark:hover:bg-blue-900/20"
                                        >
                                            <Plus class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <Label class="text-xs text-neutral-500 dark:text-neutral-400 mb-1.5 flex items-center gap-1">
                                        <Clock class="w-3 h-3" />
                                        Descanso (s)
                                    </Label>
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="exercise.rest_seconds = Math.max(0, exercise.rest_seconds - 10)"
                                            :disabled="exercise.rest_seconds <= 0"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-neutral-200 bg-white text-neutral-600 transition-all hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-neutral-200 disabled:hover:text-neutral-600 disabled:hover:bg-white dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-violet-600 dark:hover:text-violet-400 dark:hover:bg-violet-900/20 dark:disabled:hover:border-neutral-700 dark:disabled:hover:text-neutral-400 dark:disabled:hover:bg-neutral-800"
                                        >
                                            <Minus class="w-4 h-4" />
                                        </button>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-center rounded-lg border-2 border-neutral-200 bg-neutral-50 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900/50">
                                                <span class="text-xl font-bold text-neutral-900 dark:text-white">{{ exercise.rest_seconds }}</span>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            @click="exercise.rest_seconds = exercise.rest_seconds + 10"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border-2 border-neutral-200 bg-white text-neutral-600 transition-all hover:border-violet-300 hover:text-violet-600 hover:bg-violet-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-violet-600 dark:hover:text-violet-400 dark:hover:bg-violet-900/20"
                                        >
                                            <Plus class="w-4 h-4" />
                                        </button>
                                    </div>
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
                >{{ form.errors.exercises }}</span>
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
                    :disabled="form.processing || form.exercises.length === 0"
                >
                    {{ form.processing ? 'Salvando...' : 'Salvar Treino' }}
                </Button>
            </div>
        </div>
    </form>
</template>
