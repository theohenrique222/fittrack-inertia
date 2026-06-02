<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Mail, Shield, User, UserPlus } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/students';

const emit = defineEmits(['change']);

const form = useForm({
    name: '',
    email: '',
    // password: '',
    nickname: '',
});

function handleSubmit() {
    form.post(store.url(), {
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
    <form @submit.prevent="handleSubmit" class="flex flex-col h-full">
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Dados do Aluno
                </h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                    Preencha as informações para cadastrar o aluno
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
                        placeholder="aluno@email.com"
                    />
                    <span
                        v-if="form.errors.email"
                        class="text-xs text-red-500 mt-1"
                    >{{ form.errors.email }}</span>
                </div>

<!--                <div>-->
<!--                    <Label class="mb-2 flex items-center gap-1.5">-->
<!--                        <Shield class="w-3.5 h-3.5 text-neutral-400" />-->
<!--                        Senha *-->
<!--                    </Label>-->
<!--                    <Input-->
<!--                        v-model="form.password"-->
<!--                        type="password"-->
<!--                        placeholder="Mínimo 8 caracteres"-->
<!--                    />-->
<!--                    <span-->
<!--                        v-if="form.errors.password"-->
<!--                        class="text-xs text-red-500 mt-1"-->
<!--                    >{{ form.errors.password }}</span>-->
<!--                </div>-->

                <div>
                    <Label class="mb-2 flex items-center gap-1.5">
                        <UserPlus class="w-3.5 h-3.5 text-neutral-400" />
                        Apelido
                    </Label>
                    <Input
                        v-model="form.nickname"
                        type="text"
                        placeholder="Ex: João, Jão..."
                    />
                    <span
                        v-if="form.errors.nickname"
                        class="text-xs text-red-500 mt-1"
                    >{{ form.errors.nickname }}</span>
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
                    class="flex-1 cursor-pointer bg-blue-600 hover:bg-blue-700"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Salvando...' : 'Cadastrar Aluno' }}
                </Button>
            </div>
        </div>
    </form>
</template>
