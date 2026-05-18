<script setup lang="ts">
interface Props {
    data: { day: string; value: number }[];
    height?: number;
}

const props = withDefaults(defineProps<Props>(), {
    height: 160,
});

const maxValue = Math.max(...props.data.map((d) => d.value));
</script>

<template>
    <div class="flex items-end gap-2" :style="{ height: `${height}px` }">
        <div
            v-for="(item, index) in data"
            :key="item.day"
            class="flex flex-1 flex-col items-center gap-1"
        >
            <span class="text-xs font-medium text-neutral-600 dark:text-neutral-400">
                {{ item.value }}
            </span>
            <div
                class="w-full rounded-t-md bg-gradient-to-t from-emerald-600 to-emerald-400 transition-all duration-500 hover:from-emerald-700 hover:to-emerald-500"
                :style="{
                    height: `${(item.value / maxValue) * (height - 40)}px`,
                    animationDelay: `${index * 100}ms`,
                }"
            />
            <span class="text-xs text-neutral-500 dark:text-neutral-400">
                {{ item.day }}
            </span>
        </div>
    </div>
</template>
