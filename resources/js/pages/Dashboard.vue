<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';

const page = usePage();

const user = computed(() => page.props.auth.user);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Painel de controle',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Painel de controle" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">

        <!-- Header -->
        <div class="rounded-xl border p-6 bg-linear-to-r from-emerald-500 to-emerald-600 text-white shadow-md">
            <h1 class="text-2xl font-bold">
                Bem-vindo de volta, {{ user.name }} 👋
            </h1>
            <p class="text-sm opacity-90 mt-1">
                Aqui está um resumo rápido do seu desempenho hoje.
            </p>
        </div>

        <!-- Cards -->
        <div class="grid gap-4 md:grid-cols-3">

            <div class="rounded-xl border p-5 shadow-sm bg-neutral-100 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500">Clientes</p>
                <h2 class="text-2xl font-bold">--</h2>
            </div>

            <div class="rounded-xl border p-5 shadow-sm bg-neutral-100 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500">Treinos ativos</p>
                <h2 class="text-2xl font-bold">--</h2>
            </div>

            <div class="rounded-xl border p-5 shadow-sm bg-neutral-100 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500">Progresso</p>
                <h2 class="text-2xl font-bold">--</h2>
            </div>

        </div>

        <!-- Área inferior -->
        <div class="grid md:grid-cols-2 gap-4">

            <div class="rounded-xl border p-5 min-h-75 bg-neutral-100 dark:bg-neutral-900">
                <h3 class="font-semibold mb-3">Atividade recente</h3>

            </div>

            <div class="rounded-xl border p-5 min-h-75 bg-neutral-100 dark:bg-neutral-900">
                <h3 class="font-semibold mb-3">Resumo semanal</h3>

            </div>

        </div>

    </div>
</template>
