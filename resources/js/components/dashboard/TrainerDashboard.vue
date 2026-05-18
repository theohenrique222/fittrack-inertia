<script setup lang="ts">
import { computed } from 'vue';
import { Users, Dumbbell, TrendingUp, FolderOpen, UserPlus, ClipboardList, BarChart3 } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import BarChart from '@/components/dashboard/BarChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import DonutChart from '@/components/dashboard/DonutChart.vue';
import ProgressRing from '@/components/dashboard/ProgressRing.vue';

interface Props {
    stats: {
        totalClients: number;
        activeClients: number;
        totalExercises: number;
        totalCategories: number;
    };
    recentClients: {
        id: number;
        name: string;
        email: string;
        nickname: string | null;
        created_at: string | null;
    }[];
    weeklyActivity: { day: string; value: number }[];
    muscleGroupDistribution: { name: string; value: number; color: string }[];
    monthlyGrowth: { month: string; clients: number; workouts: number }[];
    upcomingWorkouts: { client: string; type: string; time: string; status: string }[];
    quickActions: { label: string; route: string; icon: string }[];
}

const props = defineProps<Props>();

const iconMap: Record<string, any> = {
    'user-plus': UserPlus,
    'dumbbell': Dumbbell,
    'clipboard-list': ClipboardList,
    'bar-chart-3': BarChart3,
};

const completionRate = computed(() => {
    if (props.stats.totalClients === 0) return 0;
    return Math.round((props.stats.activeClients / props.stats.totalClients) * 100);
});
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Welcome Header -->
        <div class="rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-6 text-white shadow-lg">
            <h1 class="text-2xl font-bold">
                Painel do Treinador
            </h1>
            <p class="text-sm opacity-90 mt-1">
                Gerencie seus clientes, treinos e acompanhe o progresso
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Total de Clientes</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.totalClients }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <Users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <ProgressRing :percentage="completionRate" :size="32" :stroke-width="4" color="#6366f1" />
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ completionRate }}% ativos</span>
                </div>
            </div>

            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Clientes Ativos</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.activeClients }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <TrendingUp class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">
                    {{ stats.totalClients - stats.activeClients }} inativos
                </p>
            </div>

            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Exercícios</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.totalExercises }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <Dumbbell class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">
                    {{ stats.totalCategories }} categorias
                </p>
            </div>

            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Categorias</p>
                        <h2 class="text-3xl font-bold mt-1">{{ stats.totalCategories }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center">
                        <FolderOpen class="w-6 h-6 text-pink-600 dark:text-pink-400" />
                    </div>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-3">
                    Grupos musculares
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <Link
                v-for="action in quickActions"
                :key="action.label"
                :href="action.route"
                class="flex items-center gap-3 rounded-xl border bg-white dark:bg-neutral-900 p-4 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600 transition-all"
            >
                <component :is="iconMap[action.icon]" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                <span class="text-sm font-medium">{{ action.label }}</span>
            </Link>
        </div>

        <!-- Charts Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Weekly Activity -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Atividade Semanal</h3>
                <BarChart :data="weeklyActivity" :height="180" />
            </div>

            <!-- Muscle Group Distribution -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Distribuição por Grupo Muscular</h3>
                <DonutChart :data="muscleGroupDistribution" :size="160" />
            </div>
        </div>

        <!-- Monthly Growth Chart -->
        <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4">Crescimento Mensal</h3>
            <LineChart :data="monthlyGrowth" :height="220" />
        </div>

        <!-- Bottom Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Recent Clients -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Clientes Recentes</h3>
                <div class="space-y-3">
                    <div
                        v-for="client in recentClients"
                        :key="client.id"
                        class="flex items-center justify-between py-2 border-b last:border-b-0"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                                {{ client.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ client.name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ client.email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ client.created_at }}</span>
                    </div>
                    <div v-if="!recentClients.length" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                        Nenhum cliente cadastrado
                    </div>
                </div>
            </div>

            <!-- Upcoming Workouts -->
            <div class="rounded-xl border bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4">Treinos do Dia</h3>
                <div class="space-y-3">
                    <div
                        v-for="(workout, index) in upcomingWorkouts"
                        :key="index"
                        class="flex items-center justify-between py-2 border-b last:border-b-0"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-semibold text-sm">
                                {{ workout.client.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ workout.client }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ workout.type }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            {{ workout.time }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
