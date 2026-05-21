<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Lock, ShieldAlert, User } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { updateFirst } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Defina sua nova senha',
        description: 'Por segurança, você precisa criar uma nova senha antes de acessar o sistema.',
    },
});

defineProps<{
    user: {
        name: string;
        email: string;
    };
}>();
</script>

<template>
    <Head title="Defina sua nova senha" />

    <div class="flex flex-col gap-6">
        <!-- Alert Banner -->
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800/50 dark:bg-amber-900/20">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/40">
                <ShieldAlert class="h-5 w-5 text-amber-600 dark:text-amber-400" />
            </div>
            <div class="space-y-1">
                <p class="text-sm font-medium text-amber-800 dark:text-amber-300">
                    Senha padrão detectada
                </p>
                <p class="text-xs text-amber-700 dark:text-amber-400/80">
                    Sua conta foi criada com uma senha temporária. Para proteger seus dados, defina uma senha pessoal agora.
                </p>
            </div>
        </div>

        <!-- User Info -->
        <div class="flex items-center gap-3 rounded-xl bg-muted/40 p-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-600">
                <User class="h-4 w-4 text-white" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-foreground">{{ user.name }}</p>
                <p class="truncate text-xs text-muted-foreground">{{ user.email }}</p>
            </div>
        </div>

        <!-- Password Form -->
        <Form
            :action="updateFirst.url()"
            method="post"
            :reset-on-success="['new_password', 'new_password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-5"
        >
            <div class="grid gap-5">
                <div class="grid gap-2.5">
                    <Label for="new_password" class="flex items-center gap-1.5 text-sm font-medium">
                        <Lock class="h-3.5 w-3.5 text-muted-foreground" />
                        Nova senha
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <PasswordInput
                            id="new_password"
                            name="new_password"
                            required
                            autofocus
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="errors.new_password" />
                </div>

                <div class="grid gap-2.5">
                    <Label for="new_password_confirmation" class="flex items-center gap-1.5 text-sm font-medium">
                        <Lock class="h-3.5 w-3.5 text-muted-foreground" />
                        Confirmar nova senha
                    </Label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <PasswordInput
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repita a nova senha"
                            class="pl-10"
                        />
                    </div>
                    <InputError :message="errors.new_password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110"
                    :disabled="processing"
                >
                    <Spinner v-if="processing" />
                    <span v-else class="flex items-center gap-2">
                        Alterar senha e acessar
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </span>
                </Button>
            </div>
        </Form>
    </div>
</template>
