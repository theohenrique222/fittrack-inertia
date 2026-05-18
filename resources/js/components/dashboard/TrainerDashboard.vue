<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Users, Dumbbell, TrendingUp, FolderOpen, UserPlus, ClipboardList, BarChart3, ArrowUpRight, ArrowDownRight } from 'lucide-vue-next';
import { computed } from 'vue';
import BarChart from '@/components/dashboard/BarChart.vue';
import DonutChart from '@/components/dashboard/DonutChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';
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
    if (props.stats.totalClients === 0) {
return 0;
}

    return Math.round((props.stats.activeClients / props.stats.totalClients) * 100);
});

const clientGrowth = computed(() => {
    if (props.stats.totalClients === 0) {
return 0;
}

    return Math.round(((props.stats.activeClients - (props.stats.totalClients - props.stats.activeClients)) / props.stats.totalClients) * 100);
});
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Welcome Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 md:p-8 text-white shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32" />
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24" />
            <div class="relative z-10">
                <h1 class="text-2xl md:text-3xl font-bold">
                    Painel do Treinador
                </h1>
                <p class="text-sm md:text-base opacity-90 mt-2 max-w-xl">
                    Gerencie seus clientes, treinos e acompanhe o progresso de forma centralizada
                </p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <TrendingUp class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ stats.activeClients }} clientes ativos</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <Dumbbell class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ stats.totalExercises }} exercícios</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Total de Clientes</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalClients }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <ArrowUpRight class="w-4 h-4 text-emerald-500" />
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ completionRate }}%</span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">ativos</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <Users class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Clientes Ativos</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.activeClients }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <ArrowDownRight class="w-4 h-4 text-neutral-400" />
                            <span class="text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ stats.totalClients - stats.activeClients }} inativos</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <TrendingUp class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Exercícios</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalExercises }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ stats.totalCategories }}</span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">categorias</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <Dumbbell class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Categorias</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalCategories }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Grupos musculares</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <FolderOpen class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Ações Rápidas</h3>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="action in quickActions"
                    :key="action.label"
                    :href="action.route"
                    class="group flex items-center gap-3 rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 p-4 hover:border-emerald-300 dark:hover:border-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200"
                >
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <component :is="iconMap[action.icon]" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-200">{{ action.label }}</span>
                </Link>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Weekly Activity -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Atividade Semanal</h3>
                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                        {{ weeklyActivity.reduce((sum, d) => sum + d.value, 0) }} treinos
                    </span>
                </div>
                <BarChart :data="weeklyActivity" :height="180" />
            </div>

            <!-- Muscle Group Distribution -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Distribuição por Grupo Muscular</h3>
                <DonutChart :data="muscleGroupDistribution" :size="160" />
            </div>
        </div>

        <!-- Monthly Growth Chart -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-neutral-900 dark:text-white">Crescimento Mensal</h3>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500" />
                        <span class="text-xs text-neutral-600 dark:text-neutral-400">Clientes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-teal-400" />
                        <span class="text-xs text-neutral-600 dark:text-neutral-400">Treinos</span>
                    </div>
                </div>
            </div>
            <LineChart :data="monthlyGrowth" :height="220" />
        </div>

        <!-- Bottom Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Recent Clients -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Clientes Recentes</h3>
                    <Link href="/students" class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">
                        Ver todos →
                    </Link>
                </div>
                <div class="space-y-1">
                    <div
                        v-for="client in recentClients"
                        :key="client.id"
                        class="flex items-center justify-between py-3 px-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                {{ client.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ client.name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ client.email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-neutral-500 dark:text-neutral-400 bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded-full">{{ client.created_at }}</span>
                    </div>
                    <div v-if="!recentClients.length" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                        Nenhum cliente cadastrado
                    </div>
                </div>
            </div>

            <!-- Upcoming Workouts -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Treinos do Dia</h3>
                <div class="space-y-1">
                    <div
                        v-for="(workout, index) in upcomingWorkouts"
                        :key="index"
                        class="flex items-center justify-between py-3 px-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                {{ workout.client.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ workout.client }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ workout.type }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-medium px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                            {{ workout.time }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
