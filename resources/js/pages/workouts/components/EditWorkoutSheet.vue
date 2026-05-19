<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
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

const workoutExercises = ref<WorkoutExercise[]>(
    props.workout.exercises?.map((ex) => ({
        exercise_id: ex.id,
        sets: ex.pivot.sets,
        reps: ex.pivot.reps,
        rest_seconds: ex.pivot.rest_seconds,
        order: ex.pivot.order,
        notes: ex.pivot.notes ?? '',
    })) ?? []
);

const form = useForm({
    name: props.workout.name,
    description: props.workout.description || '',
    student_id: String(props.workout.student_id || props.workout.client_id || ''),
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

    form.put(update.url(props.workout.id), {
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
    <form @submit.prevent="handleSubmit">
        <div>
            <div class="mb-2">
                <Label class="mb-2">Nome do Treino *</Label>
                <Input v-model="form.name" type="text" />
                <span v-if="form.errors.name" class="text-xs text-red-500">{{
                    form.errors.name
                }}</span>
            </div>

            <div class="mb-2">
                <Label class="mb-2">Descrição</Label>
                <Textarea v-model="form.description" />
                <span
                    v-if="form.errors.description"
                    class="text-xs text-red-500"
                    >{{ form.errors.description }}</span
                >
            </div>

            <div class="mb-2">
                <Label class="mb-2">Aluno *</Label>
                <div class="h-10 px-3 py-2 rounded-md border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 text-sm text-neutral-700 dark:text-neutral-300">
                    {{ props.student.name }}{{ props.student.nickname ? ` (${props.student.nickname})` : '' }}
                </div>
            </div>

            <div class="mb-2">
                <div class="flex items-center justify-between mb-2">
                    <Label>Exercícios</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addExercise"
                    >
                        <Plus class="w-4 h-4 mr-1" />
                        Adicionar
                    </Button>
                </div>

                <div
                    v-if="workoutExercises.length === 0"
                    class="text-sm text-neutral-500 py-4 text-center"
                >
                    Nenhum exercício adicionado
                </div>

                <div
                    v-for="(exercise, index) in workoutExercises"
                    :key="index"
                    class="mb-3 p-3 border rounded-lg"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium">Exercício #{{ index + 1 }}</span>
                        <Button
                            type="button"
                            variant="ghost"
                            size="sm"
                            @click="removeExercise(index)"
                        >
                            <Trash2 class="w-4 h-4 text-red-500" />
                        </Button>
                    </div>

                    <div class="mb-2">
                        <Select
                            :model-value="String(exercise.exercise_id)"
                            @update:model-value="exercise.exercise_id = Number($event)"
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione o exercício..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="ex in exercises"
                                    :key="ex.id"
                                    :value="String(ex.id)"
                                >
                                    {{ ex.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <Label class="text-xs">Séries</Label>
                            <Input
                                v-model.number="exercise.sets"
                                type="number"
                                min="1"
                                class="h-8"
                            />
                        </div>
                        <div>
                            <Label class="text-xs">Repetições</Label>
                            <Input
                                v-model.number="exercise.reps"
                                type="number"
                                min="1"
                                class="h-8"
                            />
                        </div>
                        <div>
                            <Label class="text-xs">Descanso (s)</Label>
                            <Input
                                v-model.number="exercise.rest_seconds"
                                type="number"
                                min="0"
                                class="h-8"
                            />
                        </div>
                    </div>

                    <div class="mt-2">
                        <Label class="text-xs">Observações</Label>
                        <Input
                            v-model="exercise.notes"
                            type="text"
                            class="h-8"
                            placeholder="Opcional"
                        />
                    </div>
                </div>

                <span
                    v-if="form.errors.exercises"
                    class="text-xs text-red-500"
                    >{{ form.errors.exercises }}</span
                >
            </div>

            <div class="mt-5 flex justify-around space-x-2">
                <Button
                    type="button"
                    variant="secondary"
                    class="w-1/2 cursor-pointer"
                    @click="handleCancel"
                    :disabled="form.processing"
                >
                    Cancelar
                </Button>

                <Button
                    type="submit"
                    class="w-1/2 cursor-pointer"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Salvando...' : 'Salvar' }}
                </Button>
            </div>
        </div>
    </form>
</template>
