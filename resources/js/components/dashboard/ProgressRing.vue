<script setup lang="ts">
interface Props {
    percentage: number;
    size?: number;
    strokeWidth?: number;
    color?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 80,
    strokeWidth: 6,
    color: '#10b981',
});

const radius = (props.size - props.strokeWidth) / 2;
const circumference = 2 * Math.PI * radius;
const validPercentage = Math.min(Math.max(props.percentage, 0), 100);
const offset = circumference - (validPercentage / 100) * circumference;
</script>

<template>
    <div class="relative" :style="{ width: `${size}px`, height: `${size}px` }">
        <svg :width="size" :height="size" class="-rotate-90">
            <circle
                cx="50%"
                cy="50%"
                :r="radius"
                fill="none"
                stroke="currentColor"
                :stroke-width="strokeWidth"
                class="text-neutral-200 dark:text-neutral-700"
            />
            <circle
                cx="50%"
                cy="50%"
                :r="radius"
                fill="none"
                :stroke="color"
                :stroke-width="strokeWidth"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="offset"
                stroke-linecap="round"
                class="transition-all duration-700"
            />
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
            <slot>
                <span class="text-sm font-bold">{{ percentage }}%</span>
            </slot>
        </div>
    </div>
</template>
