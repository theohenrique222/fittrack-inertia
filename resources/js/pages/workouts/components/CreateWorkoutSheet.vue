<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Dumbbell, Plus, Sparkles, Trash2 } from 'lucide-vue-next';
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
    client_id: '',
    exercises: [] as WorkoutExercise[],
    category_ids: [] as number[],
    is_active: true,
});

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
        form.exercises = workoutExercises.value.filter((e) => e.exercise_id > 0);

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

function getCategoryName(exercise: Exercise): string {
    return exercise.category?.name || '';
}

function getExercisesByCategory(categoryId: number): Exercise[] {
    return props.exercises.filter((ex) => ex.category?.id === categoryId);
}
</script>

<template>
    <form @submit.prevent="handleSubmit">
        <div>
            <div class="mb-4">
                <Label class="mb-2">Modo de Criação</Label>
                <div class="flex gap-2">
                    <Button
                        type="button"
                        :variant="mode === 'auto' ? 'default' : 'outline'"
                        size="sm"
                        class="flex-1"
                        @click="mode = 'auto'"
                    >
                        <Sparkles class="w-4 h-4 mr-1" />
                        Automático
                    </Button>
                    <Button
                        type="button"
                        :variant="mode === 'manual' ? 'default' : 'outline'"
                        size="sm"
                        class="flex-1"
                        @click="mode = 'manual'"
                    >
                        <Dumbbell class="w-4 h-4 mr-1" />
                        Manual
                    </Button>
                </div>
            </div>

            <div class="mb-2">
                <Label class="mb-2">Nome do Treino *</Label>
                <Input v-model="form.name" type="text" placeholder="Opcional no modo automático" />
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
                            {{ student.name }}{{ student.nickname ? ` (${student.nickname})` : '' }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <span
                    v-if="form.errors.client_id"
                    class="text-xs text-red-500"
                    >{{ form.errors.client_id }}</span
                >
            </div>

            <div v-if="mode === 'auto'" class="mb-2">
                <Label class="mb-2">Grupos Musculares *</Label>
                <p class="text-xs text-neutral-500 mb-2">
                    Selecione 1 ou mais grupos musculares. O treino será gerado automaticamente com exercícios variados.
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        type="button"
                        @click="toggleCategory(category.id)"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm border transition-all',
                            selectedCategoryIds.includes(category.id)
                                ? 'bg-blue-500 text-white border-blue-500'
                                : 'bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700 hover:border-blue-300',
                        ]"
                    >
                        {{ category.name }}
                    </button>
                </div>
                <div v-if="selectedCategoryIds.length > 0" class="mt-2 text-xs text-neutral-600 dark:text-neutral-400">
                    {{ selectedCategoryIds.length }} grupo(s) selecionado(s)
                </div>
            </div>

            <div v-if="mode === 'manual'" class="mb-2">
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
                                    <span v-if="ex.category" class="text-xs text-neutral-500">
                                        ({{ ex.category.name }})
                                    </span>
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
                    {{ form.processing ? 'Salvando...' : (mode === 'auto' ? 'Gerar Treino' : 'Salvar') }}
                </Button>
            </div>
        </div>
    </form>
</template>
