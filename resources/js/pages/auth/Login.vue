<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Bem-vindo de volta',
        description: 'Insira suas credenciais para acessar o sistema.',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Entrar" />

    <div
        v-if="status"
        class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400"
    >
        {{ status }}
    </div>

    <Form
        :action="store.url()"
        method="post"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-2.5">
                <Label for="email" class="text-sm font-medium">Email</Label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25v-10.5A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25zm-10.875 3l-3.375 2.25L3 9.75m18 0l-4.5 2.25L12 9.75" />
                        </svg>
                    </div>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="seu@email.com"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2.5">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-sm font-medium">Senha</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs"
                        :tabindex="5"
                    >
                        Esqueceu a senha?
                    </TextLink>
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-2 cursor-pointer">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span class="text-sm text-muted-foreground">Lembrar de mim</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <span v-else class="flex items-center gap-2">
                    Entrar
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </span>
            </Button>
        </div>

        <div
            class="rounded-lg border border-border bg-muted/30 px-4 py-3 text-center text-sm text-muted-foreground"
            v-if="canRegister"
        >
            Não tem uma conta?
            <TextLink :href="register()" :tabindex="5" class="font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">
                Cadastre-se grátis
            </TextLink>
        </div>
    </Form>
</template>
