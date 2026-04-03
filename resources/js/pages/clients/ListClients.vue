<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { createColumnHelper } from '@tanstack/vue-table';
import DataTable from '@/pages/clients/components/DataTable.vue';
import { ref } from 'vue';


import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';

import {
    SidebarMenuButton,
} from '@/components/ui/sidebar';

import CreateClientSheet from '@/pages/clients/components/CreateClientSheet.vue';


const props = defineProps<{
    title: string;
    clients: {
        name: string;
        age: number;
    }[];
}>();

const columnHelper = createColumnHelper<any>();

const columns = [
    columnHelper.accessor('name', {
        header: 'Nome',
    }),
    columnHelper.accessor('email', {
        header: 'Email',
    }),
];

// ✅ CONTROLE DO SHEET
const isOpen = ref(false);

const closeSheet = () => {
    isOpen.value = false;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: '/clients', // ✅ evita conflito com import
            },
        ],
    },
});
</script>

<template>
    <Head title="Clientes" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

        <div class="py-5 font-extrabold">
            <h1>Lista de Clientes</h1>
        </div>

        <DataTable :data="props.clients" :columns="columns" />

        <div class="w-full text-center">
            <Sheet v-model:open="isOpen">

                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="cursor-pointer bg-white text-black data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div class="w-full text-center">
                            <span>Cadastrar cliente</span>
                        </div>
                    </SidebarMenuButton>
                </SheetTrigger>

                <!-- 🔥 FIX PRINCIPAL -->
                <SheetContent @interactOutside.prevent>

                    <SheetHeader>
                        <SheetTitle>Cadastro de cliente</SheetTitle>
                    </SheetHeader>

                    <div class="px-4">
                        <CreateClientSheet @change="closeSheet" />
                    </div>

                </SheetContent>
            </Sheet>
        </div>

    </div>
</template>
