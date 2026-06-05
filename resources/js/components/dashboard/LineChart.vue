<script setup lang="ts">
interface Series {
    key: string;
    color: string;
    label: string;
}

interface Props {
    data: { month: string; students: number; workouts: number }[];
    height?: number;
    series?: Series[];
}

const props = withDefaults(defineProps<Props>(), {
    height: 200,
    series: () => [
        { key: 'students', color: '#10b981', label: 'Peso' },
        { key: 'workouts', color: '#14b8a6', label: 'Gordura' },
    ],
});

const width = 600;
const chartHeight = props.height;
const padding = { top: 10, right: 10, bottom: 10, left: 10 };
const plotWidth = width - padding.left - padding.right;
const plotHeight = chartHeight - padding.top - padding.bottom;

const xLabels = props.data.map((d) => d.month);

function getMax(key: string): number {
    return Math.max(...props.data.map((d) => d[key as keyof typeof d] as number), 1);
}

function getSvgPath(key: string): string {
    if (props.data.length < 2) {
return '';
}

    const values = props.data.map((d) => d[key as keyof typeof d] as number);
    const maxVal = getMax(key);

    const points = values.map((v, i) => ({
        x: padding.left + (i / (values.length - 1)) * plotWidth,
        y: padding.top + plotHeight - (v / maxVal) * plotHeight,
    }));

    if (points.length === 1) {
return '';
}

    let path = `M ${points[0].x},${points[0].y}`;

    for (let i = 1; i < points.length - 1; i++) {
        const cx = (points[i].x + points[i + 1].x) / 2;
        const cy = (points[i].y + points[i + 1].y) / 2;
        path += ` Q ${points[i].x},${points[i].y} ${cx},${cy}`;
    }

    const last = points[points.length - 1];
    path += ` L ${last.x},${last.y}`;

    return path;
}

function getAreaPath(key: string): string {
    const linePath = getSvgPath(key);

    if (!linePath) {
return '';
}

    const lastX = padding.left + plotWidth;
    const firstX = padding.left;
    const bottomY = padding.top + plotHeight;

    return `${linePath} L ${lastX},${bottomY} L ${firstX},${bottomY} Z`;
}

function getXPos(i: number): number {
    if (props.data.length < 2) {
return padding.left + plotWidth / 2;
}

    return padding.left + (i / (props.data.length - 1)) * plotWidth;
}
</script>

<template>
    <div class="w-full">
        <svg :viewBox="`0 0 ${width} ${chartHeight}`" class="w-full" :style="{ height: `${chartHeight}px` }">
            <defs>
                <linearGradient
                    v-for="s in series"
                    :key="`${s.key}Gradient`"
                    :id="`${s.key}Gradient`"
                    x1="0%"
                    y1="0%"
                    x2="0%"
                    y2="100%"
                >
                    <stop :offset="'0%'" :stop-color="s.color" stop-opacity="0.25" />
                    <stop :offset="'100%'" :stop-color="s.color" stop-opacity="0.02" />
                </linearGradient>
            </defs>

            <!-- Grid lines -->
            <line
                v-for="i in 4"
                :key="i"
                :x1="padding.left"
                :y1="padding.top + (plotHeight / 4) * i"
                :x2="padding.left + plotWidth"
                :y2="padding.top + (plotHeight / 4) * i"
                stroke="#e5e7eb"
                stroke-width="0.5"
                class="dark:stroke-neutral-700"
                stroke-dasharray="4,4"
            />

            <!-- Area fills -->
            <path
                v-for="s in series"
                :key="`${s.key}Area`"
                :d="getAreaPath(s.key)"
                :fill="`url(#${s.key}Gradient)`"
                class="transition-all duration-500"
            />

            <!-- Lines -->
            <path
                v-for="s in series"
                :key="`${s.key}Line`"
                :d="getSvgPath(s.key)"
                fill="none"
                :stroke="s.color"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="transition-all duration-500"
            />

            <!-- Dots -->
            <g v-for="s in series" :key="`${s.key}Dots`">
                <circle
                    v-for="(d, i) in data"
                    :key="`${s.key}-${i}`"
                    :cx="getXPos(i)"
                    :cy="padding.top + plotHeight - ((d[s.key as keyof typeof d] as number) / getMax(s.key)) * plotHeight"
                    :r="3"
                    :fill="s.color"
                    class="transition-all duration-300"
                    stroke="white"
                    stroke-width="2"
                />
            </g>
        </svg>

        <!-- X-axis labels -->
        <div class="flex justify-between mt-1.5 px-1">
            <span
                v-for="(label, i) in xLabels"
                :key="i"
                class="text-[10px] text-neutral-500 dark:text-neutral-400"
            >
                {{ label }}
            </span>
        </div>
    </div>
</template>
