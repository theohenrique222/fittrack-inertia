<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { onUnmounted, ref } from 'vue';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TwoFactorRecoveryCodes from '@/components/TwoFactorRecoveryCodes.vue';
import TwoFactorSetupModal from '@/components/TwoFactorSetupModal.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { useTwoFactorAuth } from '@/composables/useTwoFactorAuth';
import { edit } from '@/routes/security';
import { disable, enable } from '@/routes/two-factor';

type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    canManageTwoFactor: false,
    requiresConfirmation: false,
    twoFactorEnabled: false,
});

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Security settings',
                href: edit(),
            },
        ],
    },
});

const { hasSetupData, clearTwoFactorAuthData } = useTwoFactorAuth();
const showSetupModal = ref<boolean>(false);

onUnmounted(() => clearTwoFactorAuthData());
</script>

<template>
    <Head title="Security settings" />

    <div class="space-y-10">
        <div class="space-y-0.5">
            <h1 class="text-xl font-semibold tracking-tight">Segurança</h1>
            <p class="text-sm text-muted-foreground">
                Gerencie sua senha e autenticação
            </p>
        </div>

        <Card class="overflow-hidden p-0">
            <div class="h-1.5 w-full bg-gradient-to-r from-primary/80 to-primary/30" />

            <div class="p-6 sm:p-8">
                <CardHeader class="px-0 pt-0">
                    <CardTitle>Alterar senha</CardTitle>
                    <CardDescription>
                        Certifique-se de usar uma senha longa e aleatória para
                        manter sua conta segura
                    </CardDescription>
                </CardHeader>

                <CardContent class="px-0 pb-0">
                    <Form
                        :action="SecurityController.update.url()"
                        method="put"
                        :options="{
                            preserveScroll: true,
                        }"
                        reset-on-success
                        :reset-on-error="[
                            'password',
                            'password_confirmation',
                            'current_password',
                        ]"
                        class="space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-2">
                            <Label for="current_password">Senha atual</Label>
                            <PasswordInput
                                id="current_password"
                                name="current_password"
                                class="mt-1 block w-full"
                                autocomplete="current-password"
                                placeholder="Sua senha atual"
                            />
                            <InputError :message="errors.current_password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Nova senha</Label>
                            <PasswordInput
                                id="password"
                                name="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                                placeholder="Nova senha"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation"
                                >Confirmar nova senha</Label
                            >
                            <PasswordInput
                                id="password_confirmation"
                                name="password_confirmation"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                                placeholder="Confirme a nova senha"
                            />
                            <InputError
                                :message="errors.password_confirmation"
                            />
                        </div>

                        <div
                            class="flex flex-col gap-4 border-t pt-6 sm:flex-row sm:items-center"
                        >
                            <Button
                                :disabled="processing"
                                data-test="update-password-button"
                            >
                                Atualizar senha
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
                                    Senha atualizada!
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </CardContent>
            </div>
        </Card>

        <div v-if="canManageTwoFactor">
            <Card class="overflow-hidden p-0">
                <div class="h-1.5 w-full bg-gradient-to-r from-primary/80 to-primary/30" />

                <div class="p-6 sm:p-8">
                    <CardHeader class="px-0 pt-0">
                        <CardTitle>
                            Autenticação de dois fatores
                        </CardTitle>
                        <CardDescription>
                            Adicione uma camada extra de segurança à sua conta
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="px-0 pb-0 space-y-4">
                        <div v-if="!twoFactorEnabled" class="space-y-4">
                            <p class="text-sm text-muted-foreground">
                                Ao ativar a autenticação de dois fatores, você
                                será solicitado a informar um código de
                                segurança durante o login. Esse código pode ser
                                obtido em um aplicativo TOTP no seu celular.
                            </p>

                            <div>
                                <Button
                                    v-if="hasSetupData"
                                    @click="showSetupModal = true"
                                >
                                    Continuar configuração
                                </Button>
                                <Form
                                    v-else
                                    :action="enable.url()"
                                    method="post"
                                    @success="showSetupModal = true"
                                    #default="{ processing }"
                                >
                                    <Button
                                        type="submit"
                                        :disabled="processing"
                                    >
                                        Ativar 2FA
                                    </Button>
                                </Form>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <p class="text-sm text-muted-foreground">
                                Você será solicitado a informar um código de
                                segurança durante o login, que pode ser obtido
                                no aplicativo TOTP no seu celular.
                            </p>

                            <div class="relative inline">
                                <Form
                                    :action="disable.url()"
                                    method="delete"
                                    #default="{ processing }"
                                >
                                    <Button
                                        variant="destructive"
                                        type="submit"
                                        :disabled="processing"
                                    >
                                        Desativar 2FA
                                    </Button>
                                </Form>
                            </div>

                            <TwoFactorRecoveryCodes />
                        </div>
                    </CardContent>
                </div>
            </Card>

            <TwoFactorSetupModal
                v-model:isOpen="showSetupModal"
                :requiresConfirmation="requiresConfirmation"
                :twoFactorEnabled="twoFactorEnabled"
            />
        </div>
    </div>
</template>
