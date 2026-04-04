<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { createColumnHelper } from '@tanstack/vue-table';
import { ref } from 'vue';
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
import DataTable from '@/pages/clients/components/DataTable.vue';
import { Button } from '@/components/ui/button';
import { destroy } from '@/routes/clients';

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

const columnHelper = createColumnHelper<Client>();

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const selectedClient = ref<Client | null>(null);

const handleEditClick = (client: Client) => {
    selectedClient.value = client;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este cliente?')) return;
    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedClient.value = null;
        },
    });
};

const handleResetPasswordClick = (id: number) => {
    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) return;
    router.post(resetPassword.url(id), {});
};

const columns = [
    columnHelper.accessor('name', {
        header: 'Nome',
    }),
    columnHelper.accessor('email', {
        header: 'Email',
    }),
    columnHelper.accessor('nickname', {
        header: 'Apelido',
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Ações',
        cell: (props) => {
            const client = props.row.original;
            return {
                component: 'div',
                props: {
                    class: 'flex gap-2',
                },
            };
        },
    }),
];

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

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4 dark:bg-neutral-900 bg-neutral-50">

        <div class="py-5 font-extrabold">
            <h1>Lista de Clientes</h1>
        </div>

        <div class="flex flex-col gap-4">
            <div
                v-for="client in props.clients.data"
                :key="client.id"
                class="flex items-center justify-between border border-neutral-800 rounded-lg p-4"
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

        <div v-if="!props.clients.data.length" class="text-center text-neutral-500 py-8">
            Nenhum cliente cadastrado
        </div>

        <div class="w-1/2 m-auto text-center mt-4">
            <Sheet v-model:open="isCreateOpen">

                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="cursor-pointer data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground hover:bg-emerald-600 text-white  bg-emerald-500"
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

