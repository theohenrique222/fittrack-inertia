<script setup lang="ts">
import {
    getCoreRowModel,
    useVueTable,
    FlexRender
} from '@tanstack/vue-table';

const props = defineProps<{
    data: any[];
    columns: any[];
}>();

const table = useVueTable({
    get data() {
        return props.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
    <div class="rounded-xl border border-neutral-800 overflow-hidden">
        <table class="w-full text-sm text-left">
            <!-- HEADER -->
            <thead class="bg-neutral-900">
            <tr
                v-for="headerGroup in table.getHeaderGroups()"
                :key="headerGroup.id"
            >
                <th
                    v-for="header in headerGroup.headers"
                    :key="header.id"
                    class="p-3 font-medium text-neutral-400"
                >
                    <FlexRender
                        v-if="!header.isPlaceholder"
                        :render="header.column.columnDef.header"
                        :props="header.getContext()"
                    />
                </th>
            </tr>
            </thead>

            <!-- BODY -->
            <tbody>
            <tr
                v-for="row in table.getRowModel().rows"
                :key="row.id"
                class="border-t border-neutral-800 hover:bg-neutral-900/50 transition"
            >
                <td
                    v-for="cell in row.getVisibleCells()"
                    :key="cell.id"
                    class="p-3"
                >
                    <FlexRender
                        :render="cell.column.columnDef.cell"
                        :props="cell.getContext()"
                    />
                </td>
            </tr>

            <!-- EMPTY STATE -->
            <tr v-if="!table.getRowModel().rows.length">
                <td
                    :colspan="props.columns.length"
                    class="p-6 text-center text-neutral-500"
                >
                    Nenhum resultado encontrado.
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
