<script setup lang="ts">
interface Props {
    data: { name: string; value: number; color: string }[];
    size?: number;
}

const props = withDefaults(defineProps<Props>(), {
    size: 180,
});

const total = props.data.reduce((sum, d) => sum + d.value, 0);
const radius = (props.size - 20) / 2;
const circumference = 2 * Math.PI * radius;

let cumulativePercent = 0;
const segments = props.data.map((d) => {
    const percent = d.value / total;
    const offset = circumference * (1 - cumulativePercent);
    cumulativePercent += percent;

    return {
        ...d,
        percent,
        offset,
        dashArray: circumference * percent,
    };
});
</script>

<template>
    <div class="flex flex-col items-center gap-4">
        <div class="relative" :style="{ width: `${size}px`, height: `${size}px` }">
            <svg :width="size" :height="size" class="-rotate-90">
                <circle
                    cx="50%"
                    cy="50%"
                    :r="radius"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="12"
                    class="text-neutral-200 dark:text-neutral-700"
                />
                <circle
                    v-for="(segment, index) in segments"
                    :key="index"
                    cx="50%"
                    cy="50%"
                    :r="radius"
                    fill="none"
                    :stroke="segment.color"
                    stroke-width="12"
                    :stroke-dasharray="`${segment.dashArray} ${circumference}`"
                    :stroke-dashoffset="segment.offset"
                    stroke-linecap="round"
                    class="transition-all duration-500"
                />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold">{{ total }}</span>
                <span class="text-xs text-neutral-500 dark:text-neutral-400">Total</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 w-full">
            <div
                v-for="segment in segments"
                :key="segment.name"
                class="flex items-center gap-2"
            >
                <div
                    class="w-3 h-3 rounded-full flex-shrink-0"
                    :style="{ backgroundColor: segment.color }"
                />
                <span class="text-xs text-neutral-600 dark:text-neutral-400 truncate">
                    {{ segment.name }} ({{ segment.value }})
                </span>
            </div>
        </div>
    </div>
</template>
