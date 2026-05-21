<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

defineProps<{
    title: string;
    message: string;
    status: number;
}>();

function goBack() {
    router.visit('/');
}
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-neutral-50 px-4 py-12 dark:bg-neutral-900">
        <div class="mx-auto max-w-md text-center">
            <div class="mb-6 flex justify-center">
                <div
                    class="flex h-24 w-24 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30"
                >
                    <span class="text-4xl font-bold">{{ status }}</span>
                </div>
            </div>

            <h1 class="mb-4 text-2xl font-bold text-neutral-900 dark:text-white">
                {{ title }}
            </h1>

            <p class="mb-8 text-neutral-600 dark:text-neutral-400">
                {{ message }}
            </p>

            <Button @click="goBack">
                Voltar para a página inicial
            </Button>
        </div>
    </div>
</template>