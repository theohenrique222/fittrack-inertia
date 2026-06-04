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
            <div class="absolute -left-32 -top-32 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-br from-emerald-500/15 to-teal-500/10 blur-3xl" />
            <div class="absolute bottom-0 right-0 h-64 w-64 translate-x-1/4 translate-y-1/4 rounded-full bg-gradient-to-tr from-cyan-500/10 to-blue-500/10 blur-3xl" />
            <div class="absolute left-1/3 top-2/3 h-40 w-40 rounded-full bg-gradient-to-r from-violet-500/8 to-purple-500/8 blur-3xl" />
            <div class="absolute left-2/3 top-1/4 h-32 w-32 rounded-full bg-gradient-to-l from-amber-500/8 to-orange-500/8 blur-3xl" />
        </div>

        <!-- Subtle grid pattern overlay -->
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(rgba(255,255,255,.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.03)_1px,transparent_1px)] bg-[size:64px_64px] dark:bg-[linear-gradient(rgba(255,255,255,.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.02)_1px,transparent_1px)]" />

        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col items-center gap-4">
                    <Link class="flex flex-col items-center gap-3">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/25 ring-1 ring-white/10 transition-transform duration-300 hover:scale-105"
                        >
                            <AppLogoIcon class="size-8 fill-current text-white" />
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
                <div
                    class="rounded-2xl border border-border bg-card/80 p-6 shadow-lg shadow-black/5 backdrop-blur-sm transition-all duration-300"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
