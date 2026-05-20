<script setup lang="ts">
import { computed, ref } from 'vue';

interface Column {
    key: string;
    label: string;
    sortable?: boolean;
}

interface Props {
    columns: Column[];
    rows: Record<string, any>[];
    emptyMessage?: string;
}

const props = withDefaults(defineProps<Props>(), {
    emptyMessage: 'Nenhum dado disponível',
});

const sortKey = ref<string | null>(null);
const sortDirection = ref<'asc' | 'desc'>('desc');

function sort(key: string) {
    if (sortKey.value === key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDirection.value = 'asc';
    }
}

const sortedRows = computed(() => {
    if (!sortKey.value) {
        return props.rows;
    }

    return [...props.rows].sort((a, b) => {
        const aVal = a[sortKey.value!];
        const bVal = b[sortKey.value!];

        if (typeof aVal === 'number' && typeof bVal === 'number') {
            return sortDirection.value === 'asc' ? aVal - bVal : bVal - aVal;
        }

        const comparison = String(aVal).localeCompare(String(bVal));
        return sortDirection.value === 'asc' ? comparison : -comparison;
    });
});
</script>

<template>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-neutral-200 dark:border-neutral-700">
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        @click="column.sortable ? sort(column.key) : null"
                        :class="[
                            'px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400',
                            column.sortable ? 'cursor-pointer hover:text-neutral-700 dark:hover:text-neutral-200 select-none' : '',
                        ]"
                    >
                        <div class="flex items-center gap-1">
                            {{ column.label }}
                            <svg
                                v-if="column.sortable && sortKey === column.key"
                                class="w-3 h-3"
                                :class="{ 'rotate-180': sortDirection === 'asc' }"
                                viewBox="0 0 12 12"
                                fill="none"
                            >
                                <path d="M6 2L10 7H2L6 2Z" fill="currentColor" />
                            </svg>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, index) in sortedRows"
                    :key="index"
                    class="border-b border-neutral-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-800/50 transition-colors"
                >
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        class="px-4 py-3 text-sm text-neutral-700 dark:text-neutral-300"
                    >
                        <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                            {{ row[column.key] }}
                        </slot>
                    </td>
                </tr>
                <tr v-if="!sortedRows.length">
                    <td :colspan="columns.length" class="px-4 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                        {{ emptyMessage }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
