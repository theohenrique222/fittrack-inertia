<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { update } from '@/routes/plans';

interface Plan {
    id: number;
    name: string;
    description?: string;
    price: number;
    duration_months: number;
    is_active: boolean;
}

const props = defineProps<{
    plan: Plan;
}>();

const emit = defineEmits(['close']);

const form = useForm({
    name: props.plan.name,
    description: props.plan.description || '',
    price: String(props.plan.price),
    duration_months: String(props.plan.duration_months),
    is_active: props.plan.is_active,
});

function handleSubmit() {
    form.put(update.url(props.plan.id), {
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
            <div class="mb-4">
                <Label class="mb-2">Nome do Plano *</Label>
                <Input v-model="form.name" type="text" placeholder="Ex: Básico, Premium..." />
                <span v-if="form.errors.name" class="text-xs text-red-500">{{
                    form.errors.name
                }}</span>
            </div>

            <div class="mb-4">
                <Label class="mb-2">Descrição</Label>
                <Textarea v-model="form.description" placeholder="Descrição do plano..." />
                <span v-if="form.errors.description" class="text-xs text-red-500">{{
                    form.errors.description
                }}</span>
            </div>

            <div class="mb-4">
                <Label class="mb-2">Preço (R$) *</Label>
                <Input
                    v-model="form.price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0,00"
                />
                <span v-if="form.errors.price" class="text-xs text-red-500">{{
                    form.errors.price
                }}</span>
            </div>

            <div class="mb-4">
                <Label class="mb-2">Duração (meses) *</Label>
                <Input
                    v-model="form.duration_months"
                    type="number"
                    min="1"
                    placeholder="1"
                />
                <span v-if="form.errors.duration_months" class="text-xs text-red-500">{{
                    form.errors.duration_months
                }}</span>
            </div>

            <div class="mb-6 flex items-center gap-2">
                <Checkbox v-model:checked="form.is_active" id="is_active" />
                <Label for="is_active">Plano ativo</Label>
                <span v-if="form.errors.is_active" class="text-xs text-red-500">{{
                    form.errors.is_active
                }}</span>
            </div>

            <div class="flex justify-around space-x-2">
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
