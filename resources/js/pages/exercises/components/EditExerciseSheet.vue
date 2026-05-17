<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
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
import { update } from '@/routes/exercises';
import { Textarea } from '@/components/ui/textarea';

interface Exercise {
    id: number;
    name: string;
    description?: string;
    muscle_group: string;
    equipment?: string;
    difficulty: string;
    instructions?: string;
    video_url?: string;
    image?: string;
    is_active: boolean;
}

interface Props {
    exercise: Exercise;
}

const props = defineProps<Props>();

const emit = defineEmits(['close']);

const muscleGroups = [
    'Chest',
    'Back',
    'Shoulders',
    'Biceps',
    'Triceps',
    'Forearms',
    'Abs',
    'Quadriceps',
    'Hamstrings',
    'Glutes',
    'Calves',
    'Full Body',
];

const equipmentTypes = [
    'Dumbbell',
    'Barbell',
    'Machine',
    'Cable',
    'Bodyweight',
    'Resistance Band',
    'Kettlebell',
    'Smith Machine',
];

const difficulties = ['Beginner', 'Intermediate', 'Advanced'];

const form = useForm({
    name: props.exercise.name,
    description: props.exercise.description || '',
    muscle_group: props.exercise.muscle_group,
    equipment: props.exercise.equipment || 'none',
    difficulty: props.exercise.difficulty,
    instructions: props.exercise.instructions || '',
    video_url: props.exercise.video_url || '',
    image: props.exercise.image || '',
    is_active: props.exercise.is_active,
});

function handleSubmit() {
    if (form.equipment === 'none') {
        form.equipment = null;
    }

    form.put(update.url(props.exercise.id), {
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
                <Label class="mb-2">Nome do Exercício *</Label>
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

            <div class="grid grid-cols-2 gap-2">
                <div class="mb-2">
                    <Label class="mb-2">Grupo Muscular *</Label>
                    <Select v-model="form.muscle_group">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="group in muscleGroups"
                                :key="group"
                                :value="group"
                            >
                                {{ group }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span
                        v-if="form.errors.muscle_group"
                        class="text-xs text-red-500"
                        >{{ form.errors.muscle_group }}</span
                    >
                </div>

                <div class="mb-2">
                    <Label class="mb-2">Equipamento</Label>
                    <Select v-model="form.equipment">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="none">Nenhum</SelectItem>
                            <SelectItem
                                v-for="equip in equipmentTypes"
                                :key="equip"
                                :value="equip"
                            >
                                {{ equip }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <span
                        v-if="form.errors.equipment"
                        class="text-xs text-red-500"
                        >{{ form.errors.equipment }}</span
                    >
                </div>
            </div>

            <div class="mb-2">
                <Label class="mb-2">Dificuldade *</Label>
                <Select v-model="form.difficulty">
                    <SelectTrigger>
                        <SelectValue placeholder="Selecione..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="diff in difficulties"
                            :key="diff"
                            :value="diff"
                        >
                            {{ diff }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <span
                    v-if="form.errors.difficulty"
                    class="text-xs text-red-500"
                    >{{ form.errors.difficulty }}</span
                >
            </div>

            <div class="mb-2">
                <Label class="mb-2">Instruções</Label>
                <Textarea v-model="form.instructions" />
                <span
                    v-if="form.errors.instructions"
                    class="text-xs text-red-500"
                    >{{ form.errors.instructions }}</span
                >
            </div>

            <div class="mb-2">
                <Label class="mb-2">URL do Vídeo</Label>
                <Input v-model="form.video_url" type="url" />
                <span
                    v-if="form.errors.video_url"
                    class="text-xs text-red-500"
                    >{{ form.errors.video_url }}</span
                >
            </div>

            <div class="mb-2">
                <Label class="mb-2">URL da Imagem</Label>
                <Input v-model="form.image" type="text" />
                <span v-if="form.errors.image" class="text-xs text-red-500">{{
                    form.errors.image
                }}</span>
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

