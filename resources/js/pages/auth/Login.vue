<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { AlertCircle, ArrowRight, Lock, Mail } from 'lucide-vue-next';
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
        class="mb-4 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400"
    >
        <AlertCircle class="h-4 w-4 shrink-0" />
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
            <div class="grid gap-2">
                <Label for="email" class="text-sm font-medium">Email</Label>
                <div class="relative group">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 transition-colors duration-200 group-focus-within:text-emerald-500"
                    >
                        <Mail class="h-4 w-4 text-muted-foreground transition-colors duration-200 group-focus-within:text-emerald-500" />
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
                        class="pl-10 transition-all duration-200 group-focus-within:border-emerald-400 group-focus-within:ring-emerald-500/20"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-sm font-medium">Senha</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-medium text-muted-foreground transition-colors hover:text-emerald-600 dark:hover:text-emerald-400"
                        :tabindex="5"
                    >
                        Esqueceu a senha?
                    </TextLink>
                </div>
                <div class="relative group">
                    <div
                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 transition-colors duration-200 group-focus-within:text-emerald-500"
                    >
                        <Lock class="h-4 w-4 text-muted-foreground transition-colors duration-200 group-focus-within:text-emerald-500" />
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="pl-10 transition-all duration-200 group-focus-within:border-emerald-400 group-focus-within:ring-emerald-500/20"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Label
                    for="remember"
                    class="flex cursor-pointer select-none items-center gap-3"
                >
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="data-[state=checked]:border-emerald-600 data-[state=checked]:bg-emerald-600"
                    />
                    <span
                        class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                    >
                        Lembrar de mim
                    </span>
                </Label>
            </div>

            <Button
                type="submit"
                class="group mt-1 w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all duration-300 hover:scale-[1.02] hover:shadow-emerald-500/40 hover:brightness-110 active:scale-[0.98]"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                <span v-else class="flex items-center justify-center gap-2 font-medium">
                    Entrar
                    <ArrowRight class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                </span>
            </Button>
        </div>

        <div v-if="canRegister" class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border" />
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-card px-3 text-muted-foreground">ou</span>
            </div>
        </div>

        <div
            v-if="canRegister"
            class="text-center text-sm text-muted-foreground"
        >
            Não tem uma conta?
            <TextLink
                :href="register()"
                :tabindex="6"
                class="font-semibold text-emerald-600 transition-colors hover:text-emerald-500 dark:text-emerald-400"
            >
                Cadastre-se grátis
            </TextLink>
        </div>
    </Form>
</template>
