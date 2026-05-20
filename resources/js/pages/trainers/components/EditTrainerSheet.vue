<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Mail, User, UserPlus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useToast } from '@/composables/useToast';
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

const emit = defineEmits(['close']);

const { success } = useToast();

const form = useForm({
    name: props.trainer.name,
    email: props.trainer.email,
    specialty: props.trainer.specialty || '',
});

function handleSubmit() {
    form.put(update.url(props.trainer.id), {
        onSuccess: () => {
            form.reset();
            success('Treinador atualizado com sucesso.');
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
    <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Dados do Treinador
                </h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                    Atualize as informações do treinador
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <User class="w-3.5 h-3.5 text-neutral-400" />
                        Nome completo *
                    </Label>
                    <Input
                        v-model="form.name"
                        type="text"
                        placeholder="Ex: João da Silva"
                    />
                    <span
                        v-if="form.errors.name"
                        class="text-xs text-red-500 mt-1"
                    >{{ form.errors.name }}</span>
                </div>

                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <Mail class="w-3.5 h-3.5 text-neutral-400" />
                        Email *
                    </Label>
                    <Input
                        v-model="form.email"
                        type="email"
                        placeholder="treinador@email.com"
                    />
                    <span
                        v-if="form.errors.email"
                        class="text-xs text-red-500 mt-1"
                    >{{ form.errors.email }}</span>
                </div>

                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <UserPlus class="w-3.5 h-3.5 text-neutral-400" />
                        Especialidade
                    </Label>
                    <Input
                        v-model="form.specialty"
                        type="text"
                        placeholder="Ex: Musculação, CrossFit, Yoga..."
                    />
                    <span
                        v-if="form.errors.specialty"
                        class="text-xs text-red-500 mt-1"
                    >{{ form.errors.specialty }}</span>
                </div>
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
                    {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                </Button>
            </div>
        </div>
    </form>
</template>

