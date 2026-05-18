<script setup lang="ts">
import Toast from '@/components/ui/toast/Toast.vue';
import type { ToastType } from '@/components/ui/toast/Toast.vue';

export interface ToastItem {
    id: string | number;
    message: string;
    type: ToastType;
    duration?: number;
}

const toasts = defineModel<ToastItem[]>({ required: true });

function removeToast(id: string | number) {
    const index = toasts.value.findIndex((t) => t.id === id);
    if (index !== -1) {
        toasts.value.splice(index, 1);
    }
}
</script>

<template>
    <div
        v-if="toasts.length"
        class="fixed top-4 right-4 z-50 flex flex-col gap-3"
    >
        <TransitionGroup
            name="toast"
            tag="div"
            class="flex flex-col gap-3"
        >
            <Toast
                v-for="toast in toasts"
                :key="toast.id"
                :id="toast.id"
                :message="toast.message"
                :type="toast.type"
                :duration="toast.duration"
                @close="removeToast"
            />
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active {
    transition: all 0.3s ease-out;
}

.toast-leave-active {
    transition: all 0.2s ease-in;
}

.toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.toast-enter-to {
    transform: translateX(0);
    opacity: 1;
}

.toast-leave-from {
    transform: translateX(0);
    opacity: 1;
}

.toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.toast-move {
    transition: transform 0.3s ease;
}
</style>
