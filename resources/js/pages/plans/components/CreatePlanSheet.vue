<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { AlertCircle, Calendar, DollarSign, Hash, Tag } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/routes/plans';

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    description: '',
    price: '',
    duration_months: '1',
    is_active: true,
});

function handleSubmit() {
    form.post(store.url(), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}

function handleCancel() {
    form.reset();
    emit('close');
}
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex flex-1 flex-col">
        <div class="flex-1 space-y-5 overflow-y-auto px-4 pb-4">
            <div class="space-y-2">
                <Label for="name" class="text-sm font-medium">
                    Nome do Plano <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <Tag class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Ex: Básico, Premium..."
                        class="pl-9"
                        :class="{ 'border-destructive ring-destructive/50': form.errors.name }"
                    />
                </div>
                <p v-if="form.errors.name" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="description" class="text-sm font-medium">
                    Descrição
                </Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Descreva os benefícios e detalhes do plano..."
                    class="min-h-[100px] resize-none"
                    :class="{ 'border-destructive ring-destructive/50': form.errors.description }"
                />
                <p v-if="form.errors.description" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.description }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="price" class="text-sm font-medium">
                    Preço <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <DollarSign class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="price"
                        v-model="form.price"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0,00"
                        class="pl-9"
                        :class="{ 'border-destructive ring-destructive/50': form.errors.price }"
                    />
                </div>
                <p v-if="form.errors.price" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.price }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="duration_months" class="text-sm font-medium">
                    Duração <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <Calendar class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="duration_months"
                        v-model="form.duration_months"
                        type="number"
                        min="1"
                        placeholder="1"
                        class="pl-9"
                        :class="{ 'border-destructive ring-destructive/50': form.errors.duration_months }"
                    />
                </div>
                <p v-if="form.errors.duration_months" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.duration_months }}
                </p>
                <p class="text-xs text-muted-foreground">Número de meses de vigência do plano</p>
            </div>

            <div
                class="flex cursor-pointer items-center gap-3 rounded-lg border border-border px-4 py-3 transition-colors hover:bg-accent/50 has-[[data-state=checked]]:border-primary"
                @click="form.is_active = !form.is_active"
            >
                <Checkbox
                    id="is_active"
                    :checked="form.is_active"
                    @update:checked="form.is_active = $event"
                />
                <div class="flex flex-col">
                    <Label for="is_active" class="cursor-pointer text-sm font-medium">
                        Plano ativo
                    </Label>
                    <p class="text-xs text-muted-foreground">
                        Planos inativos não podem ser atribuídos a novos alunos
                    </p>
                </div>
            </div>
            <p v-if="form.errors.is_active" class="flex items-center gap-1 text-xs text-destructive">
                <AlertCircle class="h-3 w-3" />
                {{ form.errors.is_active }}
            </p>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-border p-4">
            <Button
                type="button"
                variant="outline"
                @click="handleCancel"
                :disabled="form.processing"
            >
                Cancelar
            </Button>

            <Button
                type="submit"
                :disabled="form.processing"
            >
                <template v-if="form.processing">
                    <Hash class="h-4 w-4 animate-spin" />
                    Salvando...
                </template>
                <template v-else>
                    Criar Plano
                </template>
            </Button>
        </div>
    </form>
</template>
