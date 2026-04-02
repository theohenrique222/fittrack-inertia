<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { createColumnHelper } from '@tanstack/vue-table';
import DataTable from '@/pages/clients/components/DataTable.vue';
// eslint-disable-next-line vue/no-dupe-keys
import { clients } from '@/routes';

const props = defineProps<{
    title: any;
    clients: {
        name: string;
        age: number;
    }[];
}>();

const columnHelper = createColumnHelper();

const columns = [
    columnHelper.accessor('name', {
        header: 'Nome',
    }),
    columnHelper.accessor('age', {
        header: 'Idade',
    }),
];
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes',
                href: clients(),
            },
        ],
    },
});
</script>
<template>
    <Head title="Clientes" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="py-5 font-extrabold">
            <h1>Lista de Clientes</h1>
        </div>
        <DataTable :data="props.clients" :columns="columns" />
    </div>
</template>
