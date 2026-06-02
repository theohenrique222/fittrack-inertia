<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Calendar, UserRound } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
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
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineOptions({
    layout: {
        title: 'Crie sua conta',
        description: 'Preencha seus dados para começar gratuitamente.',
    },
});
</script>

<template>
    <Head title="Cadastro" />

    <Form
        :action="store.url()"
        method="post"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-2.5">
                <Label for="name" class="text-sm font-medium">Nome completo</Label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Seu nome completo"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.name" />
            </div>

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
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="seu@email.com"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2.5">
                <Label for="gender" class="text-sm font-medium">Gênero</Label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 z-10 flex items-center pl-3">
                        <UserRound class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <Select name="gender" required>
                        <SelectTrigger class="w-full pl-10">
                            <SelectValue placeholder="Selecione" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="male">Masculino</SelectItem>
                            <SelectItem value="female">Feminino</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <InputError :message="errors.gender" />
            </div>

            <div class="grid gap-2.5">
                <Label for="birthdate" class="text-sm font-medium">Data de Nascimento</Label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Calendar class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <Input
                        id="birthdate"
                        type="date"
                        required
                        :tabindex="3"
                        name="birthdate"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.birthdate" />
            </div>

            <div class="grid gap-2.5">
                <Label for="password" class="text-sm font-medium">Senha</Label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <PasswordInput
                        id="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Mínimo 8 caracteres"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2.5">
                <Label for="password_confirmation" class="text-sm font-medium">Confirmar senha</Label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <PasswordInput
                        id="password_confirmation"
                        required
                        :tabindex="6"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Repita sua senha"
                        class="pl-10"
                    />
                </div>
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110"
                tabindex="7"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                <span v-else class="flex items-center gap-2">
                    Criar conta
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </span>
            </Button>
        </div>

        <div class="rounded-lg border border-border bg-muted/30 px-4 py-3 text-center text-sm text-muted-foreground">
            Já tem uma conta?
            <TextLink
                :href="login()"
                class="font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400"
                :tabindex="8"
            >Faça login</TextLink>
        </div>
    </Form>
</template>
