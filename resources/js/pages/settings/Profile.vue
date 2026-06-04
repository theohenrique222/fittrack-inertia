<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
    nickname?: string | null;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Configurações do perfil',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Configurações do perfil" />

    <h1 class="sr-only">Configurações do perfil</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Configurações do perfil"
            description="Atualize seu nome e endereço de email"
        />

        <Card>
            <CardHeader>
                <CardTitle>Informações do perfil</CardTitle>
                <CardDescription>
                    Atualize seu nome e endereço de email
                </CardDescription>
            </CardHeader>

            <CardContent>
                <Form
                    :action="ProfileController.update.url()"
                    method="patch"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nome</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Nome completo"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="nickname">Como gosta de ser chamado</Label>
                        <Input
                            id="nickname"
                            name="nickname"
                            :default-value="nickname"
                            autocomplete="nickname"
                            placeholder="Ex: Joãozinho, Jhow, etc."
                        />
                        <InputError :message="errors.nickname" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Endereço de email</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="username"
                            placeholder="seu@email.com"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div
                        v-if="mustVerifyEmail && !user.email_verified_at"
                        class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800 dark:bg-amber-950"
                    >
                        <p class="text-sm text-amber-800 dark:text-amber-200">
                            Seu endereço de email não foi verificado.
                            <TextLink :href="send()">
                                Clique aqui para reenviar o email de verificação.
                            </TextLink>
                        </p>

                        <p
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-700 dark:text-green-400"
                        >
                            Um novo link de verificação foi enviado para o seu
                            endereço de email.
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-profile-button"
                        >
                            Salvar
                        </Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Salvo.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
