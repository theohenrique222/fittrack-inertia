<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Recuperar senha',
        description: 'Informe seu email para receber o link de recuperação.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Recuperar senha" />

    <div
        v-if="status"
        class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-center text-sm font-medium text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400"
    >
        {{ status }}
    </div>

    <div class="space-y-5">
        <div class="mb-4 flex items-center justify-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500/10">
                <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
        </div>

        <Form :action="email.url()" method="post" v-slot="{ errors, processing }">
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
                        autocomplete="off"
                        autofocus
                        placeholder="seu@email.com"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="mt-4">
                <Button
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" />
                    <span v-else>Enviar link de recuperação</span>
                </Button>
            </div>
        </Form>

        <div class="rounded-lg border border-border bg-muted/30 px-4 py-3 text-center text-sm text-muted-foreground">
            <span>Lembrou a senha? </span>
            <TextLink :href="login()" class="font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">Voltar ao login</TextLink>
        </div>
    </div>
</template>
