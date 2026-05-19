<script setup lang="ts">
import { ref, watch } from 'vue';

const periods = [
    { value: '7d', label: '7 dias' },
    { value: '30d', label: '30 dias' },
    { value: '90d', label: '90 dias' },
    { value: '12m', label: '12 meses' },
    { value: 'custom', label: 'Personalizado' },
];

const props = defineProps<{
    selected: string;
}>();

const emit = defineEmits<{
    change: [period: string];
}>();

const selectedPeriod = ref(props.selected);

watch(selectedPeriod, (value) => {
    emit('change', value);
});
</script>

<template>
    <div class="flex items-center gap-1 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-1">
        <button
            v-for="period in periods"
            :key="period.value"
            @click="selectedPeriod = period.value"
            :class="[
                'px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200',
                selectedPeriod === period.value
                    ? 'bg-emerald-600 text-white shadow-sm'
                    : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800',
            ]"
        >
            {{ period.label }}
        </button>
    </div>
</template>
