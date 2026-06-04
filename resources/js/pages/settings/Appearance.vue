<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Appearance settings" />

    <div class="space-y-10">
        <div class="space-y-0.5">
            <h1 class="text-xl font-semibold tracking-tight">Aparência</h1>
            <p class="text-sm text-muted-foreground">
                Personalize a aparência da sua conta
            </p>
        </div>

        <Card class="overflow-hidden p-0">
            <div class="h-1.5 w-full bg-gradient-to-r from-primary/80 to-primary/30" />

            <div class="p-6 sm:p-8">
                <CardHeader class="px-0 pt-0">
                    <CardTitle>Tema</CardTitle>
                    <CardDescription>
                        Escolha entre tema claro, escuro ou automático
                    </CardDescription>
                </CardHeader>

                <CardContent class="px-0 pb-0">
                    <AppearanceTabs />
                </CardContent>
            </div>
        </Card>
    </div>
</template>
