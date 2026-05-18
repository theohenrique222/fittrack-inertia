<script setup lang="ts">
import { computed } from 'vue';
import { CheckCircle2, AlertCircle, AlertTriangle, Info, X } from 'lucide-vue-next';

export type ToastType = 'success' | 'error' | 'warning' | 'info';

interface Props {
    id: string | number;
    message: string;
    type?: ToastType;
    duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'success',
    duration: 4000,
});

const emit = defineEmits<{
    close: [id: string | number];
}>();

const iconMap = {
    success: CheckCircle2,
    error: AlertCircle,
    warning: AlertTriangle,
    info: Info,
};

const iconColors = {
    success: 'text-emerald-500',
    error: 'text-red-500',
    warning: 'text-amber-500',
    info: 'text-blue-500',
};

const bgColors = {
    success: 'bg-emerald-50 dark:bg-emerald-950/50',
    error: 'bg-red-50 dark:bg-red-950/50',
    warning: 'bg-amber-50 dark:bg-amber-950/50',
    info: 'bg-blue-50 dark:bg-blue-950/50',
};

const borderColors = {
    success: 'border-emerald-200 dark:border-emerald-800',
    error: 'border-red-200 dark:border-red-800',
    warning: 'border-amber-200 dark:border-amber-800',
    info: 'border-blue-200 dark:border-blue-800',
};

const titles = {
    success: 'Sucesso',
    error: 'Erro',
    warning: 'Atenção',
    info: 'Informação',
};

const IconComponent = computed(() => iconMap[props.type]);

let timeout: ReturnType<typeof setTimeout>;

function startTimer() {
    if (props.duration > 0) {
        timeout = setTimeout(() => {
            emit('close', props.id);
        }, props.duration);
    }
}

function clearTimer() {
    clearTimeout(timeout);
}

startTimer();
</script>

<template>
    <div
        :class="[
            'pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border p-4 shadow-lg backdrop-blur-sm transition-all duration-300 animate-in slide-in-from-right-full',
            bgColors[type],
            borderColors[type],
        ]"
        @mouseenter="clearTimer"
        @mouseleave="startTimer"
    >
        <component
            :is="IconComponent"
            :class="['h-5 w-5 shrink-0', iconColors[type]]"
        />

        <div class="flex-1">
            <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                {{ titles[type] }}
            </p>
            <p class="mt-0.5 text-sm text-neutral-600 dark:text-neutral-400">
                {{ message }}
            </p>
        </div>

        <button
            class="shrink-0 rounded-md p-1 text-neutral-400 transition-colors hover:bg-neutral-200 hover:text-neutral-600 dark:hover:bg-neutral-800 dark:hover:text-neutral-300"
            @click="emit('close', id)"
        >
            <X class="h-4 w-4" />
        </button>
    </div>
</template>
