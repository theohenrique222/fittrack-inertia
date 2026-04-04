<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/trainers';

interface Trainer {
    id: number;
    name: string;
    email: string;
    specialty?: string;
}

const props = defineProps<{
    trainer: Trainer;
}>();

const emit = defineEmits(['change']);

const form = useForm({
    name: props.trainer.name,
    email: props.trainer.email,
    specialty: props.trainer.specialty || '',
});

function handleSubmit() {
    form.put(update.url(props.trainer.id), {
        onSuccess: () => {
            form.reset();
            emit('change');
        },
    });
}

function handleCancel() {
    form.reset();
    emit('change');
}
</script>

<template>
    <form @submit.prevent="handleSubmit">
        <div>
            <div class="mb-5">
                <h1 class="text-xl font-extrabold">Editar Treinador</h1>
                <p class="text-xs font-extralight">
                    Atualize os dados do treinador
                </p>
            </div>

            <div class="mb-2">
                <Label class="mb-2">Nome completo *</Label>
                <Input v-model="form.name" type="text" />
                <span v-if="form.errors.name" class="text-xs text-red-500">{{
                    form.errors.name
                }}</span>
            </div>

            <div class="mb-2">
                <Label class="mb-2">Email *</Label>
                <Input v-model="form.email" type="email" />
                <span v-if="form.errors.email" class="text-xs text-red-500">
                    {{ form.errors.email }}
                </span>
            </div>

            <div class="mb-2">
                <Label class="mb-2">Especialidade</Label>
                <Input v-model="form.specialty" type="text" />
                <span
                    v-if="form.errors.specialty"
                    class="text-xs text-red-500"
                    >
                    {{ form.errors.specialty }}
                </span
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

