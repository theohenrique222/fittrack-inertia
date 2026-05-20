<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center bg-background p-6 md:p-10"
    >
        <!-- Background decoration -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute left-1/4 top-1/4 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-br from-emerald-500/10 to-teal-500/10 blur-3xl" />
            <div class="absolute bottom-1/4 right-1/4 h-48 w-48 rounded-full bg-gradient-to-tr from-cyan-500/10 to-blue-500/10 blur-3xl" />
        </div>

        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        class="flex flex-col items-center gap-3"
                    >
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25"
                        >
                            <AppLogoIcon
                                class="size-8 fill-current text-white"
                            />
                        </div>
                    </Link>
                    <div class="space-y-1.5 text-center">
                        <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
                        <p class="text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>

                <!-- Card container -->
                <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
