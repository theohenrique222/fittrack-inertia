<script setup lang="ts">
interface Props {
    data: { month: string; students: number; workouts: number }[];
    height?: number;
}

const props = withDefaults(defineProps<Props>(), {
    height: 200,
});

const maxStudents = Math.max(...props.data.map((d) => d.students));
const maxWorkouts = Math.max(...props.data.map((d) => d.workouts));
const width = 600;
const chartHeight = props.height;

const studentsPoints = props.data
    .map((d, i) => `${(i / (props.data.length - 1)) * width},${chartHeight - (d.students / maxStudents) * (chartHeight - 40) - 20}`)
    .join(' ');

const workoutsPoints = props.data
    .map((d, i) => `${(i / (props.data.length - 1)) * width},${chartHeight - (d.workouts / maxWorkouts) * (chartHeight - 40) - 20}`)
    .join(' ');
</script>

<template>
    <div class="w-full">
        <svg :viewBox="`0 0 ${width} ${chartHeight}`" class="w-full" :style="{ height: `${chartHeight}px` }">
            <defs>
                <linearGradient id="studentsGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.3" />
                    <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
                </linearGradient>
                <linearGradient id="workoutsGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#14b8a6" stop-opacity="0.3" />
                    <stop offset="100%" stop-color="#14b8a6" stop-opacity="0" />
                </linearGradient>
            </defs>

            <polyline
                :points="studentsPoints"
                fill="none"
                stroke="#10b981"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />
            <polyline
                :points="workoutsPoints"
                fill="none"
                stroke="#14b8a6"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            />

            <circle
                v-for="(d, i) in data"
                :key="`students-${i}`"
                :cx="(i / (data.length - 1)) * width"
                :cy="chartHeight - (d.students / maxStudents) * (chartHeight - 40) - 20"
                r="3"
                fill="#10b981"
            />
            <circle
                v-for="(d, i) in data"
                :key="`workouts-${i}`"
                :cx="(i / (data.length - 1)) * width"
                :cy="chartHeight - (d.workouts / maxWorkouts) * (chartHeight - 40) - 20"
                r="3"
                fill="#14b8a6"
            />
        </svg>

        <div class="flex justify-between mt-2 px-1">
            <span
                v-for="d in data"
                :key="d.month"
                class="text-xs text-neutral-500 dark:text-neutral-400"
            >
                {{ d.month }}
            </span>
        </div>

        <div class="flex items-center gap-4 mt-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-emerald-500" />
                <span class="text-xs text-neutral-600 dark:text-neutral-400">Clientes</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-teal-500" />
                <span class="text-xs text-neutral-600 dark:text-neutral-400">Treinos</span>
            </div>
        </div>
    </div>
</template>
