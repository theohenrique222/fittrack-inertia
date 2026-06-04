<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
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
import { useInitials } from '@/composables/useInitials';
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
const { getInitials } = useInitials();

const photoPreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const removePhoto = ref(false);

function onFileSelected(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        photoPreview.value = URL.createObjectURL(file);
        removePhoto.value = false;
    }
}

function triggerFileInput() {
    fileInput.value?.click();
}

function clearPhoto() {
    photoPreview.value = null;
    removePhoto.value = true;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}
</script>

<template>
    <Head title="Configurações do perfil" />

    <div class="space-y-10">
        <div class="flex items-center gap-5">
            <Avatar class="h-14 w-14 rounded-full ring-2 ring-border">
                <AvatarImage
                    v-if="photoPreview ?? user.avatar"
                    :src="photoPreview ?? user.avatar!"
                    :alt="user.name"
                />
                <AvatarFallback class="rounded-full text-lg font-medium">
                    {{ getInitials(user.name) }}
                </AvatarFallback>
            </Avatar>
            <div class="space-y-0.5">
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ user.name }}
                </h1>
                <p class="text-sm text-muted-foreground">
                    Gerencie suas informações pessoais
                </p>
            </div>
        </div>

        <Card class="overflow-hidden p-0">
            <div class="h-1.5 w-full bg-gradient-to-r from-primary/80 to-primary/30" />

            <div class="p-6 sm:p-8">
                <CardHeader class="px-0 pt-0">
                    <CardTitle>Informações do perfil</CardTitle>
                    <CardDescription>
                        Atualize seu nome, email e foto de perfil
                    </CardDescription>
                </CardHeader>

                <CardContent class="px-0 pb-0">
                    <Form
                        :action="ProfileController.update.url()"
                        method="patch"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="flex items-center gap-4">
                            <Avatar class="h-20 w-20 rounded-full ring-2 ring-border">
                                <AvatarImage
                                    v-if="photoPreview ?? user.avatar"
                                    :src="photoPreview ?? user.avatar!"
                                    :alt="user.name"
                                />
                                <AvatarFallback class="rounded-full text-xl font-medium">
                                    {{ getInitials(user.name) }}
                                </AvatarFallback>
                            </Avatar>

                            <div class="flex flex-col gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="triggerFileInput"
                                >
                                    Alterar foto
                                </Button>

                                <Button
                                    v-if="user.avatar || photoPreview"
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive"
                                    @click="clearPhoto"
                                >
                                    Remover foto
                                </Button>
                            </div>

                            <input
                                ref="fileInput"
                                name="profile_photo"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @input="onFileSelected"
                            />

                            <input
                                name="remove_photo"
                                type="hidden"
                                :value="removePhoto ? '1' : '0'"
                            />
                        </div>

                        <InputError :message="errors.profile_photo" />

                        <div class="grid gap-2">
                            <Label for="name">Nome completo</Label>
                            <Input
                                id="name"
                                name="name"
                                :default-value="user.name"
                                required
                                autocomplete="name"
                                placeholder="Seu nome completo"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="nickname">Apelido</Label>
                            <Input
                                id="nickname"
                                name="nickname"
                                :default-value="nickname"
                                autocomplete="nickname"
                                placeholder="Como gosta de ser chamado"
                            />
                            <InputError :message="errors.nickname" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">E-mail</Label>
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
                            class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800 dark:bg-amber-950"
                        >
                            <p class="text-sm text-amber-800 dark:text-amber-200">
                                Seu endereço de email não foi verificado.
                                <TextLink :href="send()">
                                    Clique aqui para reenviar o email de
                                    verificação.
                                </TextLink>
                            </p>
                            <p
                                v-if="status === 'verification-link-sent'"
                                class="mt-2 text-sm font-medium text-green-700 dark:text-green-400"
                            >
                                Um novo link de verificação foi enviado.
                            </p>
                        </div>

                        <div
                            class="flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center"
                        >
                            <Button :disabled="processing">
                                Salvar alterações
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-green-600"
                                >
                                    Salvo com sucesso!
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </CardContent>
            </div>
        </Card>
    </div>
</template>
