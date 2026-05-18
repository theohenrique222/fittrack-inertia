import { ref } from 'vue';
import type { ToastItem } from '@/components/ui/toast';
import type { ToastType } from '@/components/ui/toast';

const toasts = ref<ToastItem[]>([]);
let nextId = 1;

export function useToast() {
    function addToast(message: string, type: ToastType = 'success', duration = 4000) {
        const id = nextId++;
        toasts.value.push({ id, message, type, duration });
    }

    function success(message: string, duration = 4000) {
        addToast(message, 'success', duration);
    }

    function error(message: string, duration = 5000) {
        addToast(message, 'error', duration);
    }

    function warning(message: string, duration = 4000) {
        addToast(message, 'warning', duration);
    }

    function info(message: string, duration = 4000) {
        addToast(message, 'info', duration);
    }

    return {
        toasts,
        addToast,
        success,
        error,
        warning,
        info,
    };
}
