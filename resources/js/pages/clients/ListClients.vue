<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { SidebarMenuButton } from '@/components/ui/sidebar';
import CreateClientSheet from '@/pages/clients/components/CreateClientSheet.vue';
import EditClientSheet from '@/pages/clients/components/EditClientSheet.vue';
import { destroy, resetPassword } from '@/routes/clients';

interface Client {
    id: number;
    name: string;
    email: string;
    nickname?: string;
}

const props = defineProps<{
    title: string;
    clients: {
        data: Client[];
    };
}>();

const page = usePage();

const canCreateClient = page.props.auth.can.create_client;

console.log(page.props.auth.can);

const successMessage = ref('');

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            successMessage.value = flash.success;
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        }
    },
    { immediate: true },
);

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const selectedClient = ref<Client | null>(null);

const handleEditClick = (client: Client) => {
    selectedClient.value = client;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este cliente?')) {
        return;
    }

    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedClient.value = null;
        },
    });
};

const handleResetPasswordClick = (id: number) => {
    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) {
        return;
    }

    router.post(resetPassword.url(id), {});
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: '/clients',
            },
        ],
    },
});
</script>

<template>
    <Head title="Clientes" />

    <div
        v-if="successMessage"
        class="fixed top-4 right-4 rounded-lg bg-green-500 px-4 py-2 text-white shadow-lg"
    >
        {{ successMessage }}
    </div>

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl bg-neutral-50 p-4 dark:bg-neutral-900"
    >
        <div class="py-5 font-extrabold">
            <h1>Lista de Clientes</h1>
        </div>

        <div class="flex flex-col gap-4">
            <div
                v-for="client in props.clients.data"
                :key="client.id"
                class="flex items-center justify-between rounded-lg border border-neutral-800 p-4"
            >
                <div class="flex flex-col">
                    <h3 class="font-semibold">{{ client.name }}</h3>
                    <p class="text-sm text-neutral-500">{{ client.email }}</p>
                    <p v-if="client.nickname" class="text-sm text-neutral-500">
                        {{ client.nickname }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        size="sm"
                        variant="outline"
                        @click="handleEditClick(client)"
                    >
                        Editar
                    </Button>
                    <Button
                        size="sm"
                        variant="secondary"
                        class="cursor-pointer"
                        @click="handleResetPasswordClick(client.id)"
                    >
                        Redefinir Senha
                    </Button>
                    <Button
                        size="sm"
                        variant="destructive"
                        @click="handleDeleteClick(client.id)"
                    >
                        Deletar
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="!props.clients.data.length"
            class="py-8 text-center text-neutral-500"
        >
            Nenhum cliente cadastrado
        </div>

        <div v-if="canCreateClient" class="m-auto mt-4 w-1/2 text-center">
            <Sheet v-model:open="isCreateOpen">
                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="cursor-pointer bg-emerald-500 text-white hover:bg-emerald-600 data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div class="w-full text-center">
                            <span>Cadastrar Cliente</span>
                        </div>
                    </SidebarMenuButton>
                </SheetTrigger>

                <SheetContent @interactOutside.prevent>
                    <SheetHeader>
                        <SheetTitle>Cadastro de Cliente</SheetTitle>
                    </SheetHeader>

                    <div class="px-4">
                        <CreateClientSheet @change="closeCreateSheet" />
                    </div>
                </SheetContent>
            </Sheet>
        </div>

        <Sheet v-model:open="isEditOpen">
            <SheetContent @interactOutside.prevent>
                <SheetHeader>
                    <SheetTitle>Editar Cliente</SheetTitle>
                </SheetHeader>

                <div class="px-4">
                    <EditClientSheet
                        v-if="selectedClient"
                        :client="selectedClient"
                        @change="closeEditSheet"
                    />
                </div>
            </SheetContent>
        </Sheet>
    </div>
</template>
