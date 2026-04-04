<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
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
import CreateTrainerSheet from '@/pages/trainers/components/CreateTrainerSheet.vue';
import DataTable from '@/pages/trainers/components/DataTable.vue';

const props = defineProps<{
    title: string;
    trainers: {
        data: {
            id: number;
            name: string;
            email: string;
        }[];
    };
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

const isOpen = ref(false);

const closeSheet = () => {
    isOpen.value = false;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Treinadores',
                href: '/trainers',
            },
        ],
    },
});
</script>

<template>
    <Head title="Treinadores" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="py-5 font-extrabold">
            <h1>Lista de Treinadores</h1>
        </div>

        <DataTable :data="props.trainers.data" :columns="columns" />

        <div class="w-full text-center">
            <Sheet v-model:open="isOpen">
                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="cursor-pointer bg-white text-black data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div class="w-full text-center">
                            <span>Cadastrar Treinador</span>
                        </div>
                    </SidebarMenuButton>
                </SheetTrigger>

                <SheetContent @interactOutside.prevent>
                    <SheetHeader>
                        <SheetTitle>Cadastro de Treinador</SheetTitle>
                    </SheetHeader>

                    <div class="px-4">
                        <CreateTrainerSheet @change="closeSheet" />
                    </div>
                </SheetContent>
            </Sheet>
        </div>
    </div>
</template>
