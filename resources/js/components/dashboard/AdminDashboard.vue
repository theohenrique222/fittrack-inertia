<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { Users, UserCheck, Dumbbell, FolderOpen, TrendingUp, ArrowUpRight, ArrowDownRight, UserPlus, Activity, Server, Database, BarChart3, Star, Shield, Zap } from 'lucide-vue-next';
import { computed } from 'vue';
import BarChart from '@/components/dashboard/BarChart.vue';
import DonutChart from '@/components/dashboard/DonutChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import ProgressRing from '@/components/dashboard/ProgressRing.vue';

interface Props {
    stats: {
        totalUsers: number;
        totalClients: number;
        totalTrainers: number;
        totalExercises: number;
    };
    usersByRole: { role: string; count: number; color: string }[];
    monthlyGrowth: { month: string; users: number; trainers: number }[];
    systemActivity: { day: string; logins: number; registrations: number }[];
    recentUsers: { id: number; name: string; email: string; role: string; created_at: string | null }[];
    topTrainers: { name: string; clients: number; specialty: string; rating: number }[];
    platformMetrics: { label: string; value: string; change: string; trend: string }[];
    quickActions: { label: string; route: string; icon: string }[];
}

const props = defineProps<Props>();

const page = usePage();
const user = computed(() => page.props.auth.user);

const iconMap: Record<string, any> = {
    'users': Users,
    'user-check': UserCheck,
    'dumbbell': Dumbbell,
    'bar-chart-3': BarChart3,
};

const userGrowthRate = computed(() => {
    if (props.monthlyGrowth.length < 2) {
return 0;
}

    const current = props.monthlyGrowth[props.monthlyGrowth.length - 1].users;
    const previous = props.monthlyGrowth[props.monthlyGrowth.length - 2].users;

    return Math.round(((current - previous) / previous) * 100);
});

const totalLogins = computed(() => {
    return props.systemActivity.reduce((sum, d) => sum + d.logins, 0);
});

const totalRegistrations = computed(() => {
    return props.systemActivity.reduce((sum, d) => sum + d.registrations, 0);
});

const activityChartData = computed(() => {
    return props.systemActivity.map((d) => ({
        day: d.day,
        value: d.logins,
    }));
});
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Welcome Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 md:p-8 text-white shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32" />
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24" />
            <div class="absolute top-1/2 left-1/2 w-32 h-32 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2" />
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <Shield class="w-8 h-8" />
                    <h1 class="text-2xl md:text-3xl font-bold">
                        Bem-vindo de volta, {{ user.name }}! 👋
                    </h1>
                </div>
                <p class="text-sm md:text-base opacity-90 mt-2 max-w-xl">
                    Painel administrativo do FitTrack - Gerencie usuários, treinadores e acompanhe as métricas da plataforma
                </p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <Users class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ stats.totalUsers }} usuários</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <TrendingUp class="w-4 h-4" />
                        <span class="text-sm font-medium">+{{ userGrowthRate }}% este mês</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2">
                        <Activity class="w-4 h-4" />
                        <span class="text-sm font-medium">{{ totalLogins }} logins esta semana</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Total de Usuários</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalUsers }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <ArrowUpRight class="w-4 h-4 text-emerald-500" />
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">+{{ userGrowthRate }}%</span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">este mês</span>
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
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Clientes</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalClients }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ Math.round((stats.totalClients / stats.totalUsers) * 100) }}% do total</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <UserPlus class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Treinadores</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalTrainers }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Ativos na plataforma</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <UserCheck class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">Exercícios</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ stats.totalExercises }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">Disponíveis</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <Dumbbell class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
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
            <!-- System Activity -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Atividade do Sistema</h3>
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                            {{ totalLogins }} logins
                        </span>
                        <span class="text-xs px-2 py-1 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 font-medium">
                            {{ totalRegistrations }} novos
                        </span>
                    </div>
                </div>
                <BarChart :data="activityChartData" :height="180" />
            </div>

            <!-- Users by Role -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Distribuição por Perfil</h3>
                <DonutChart :data="usersByRole.map(r => ({ name: r.role, value: r.count, color: r.color }))" :size="160" />
            </div>
        </div>

        <!-- Monthly Growth Chart -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-neutral-900 dark:text-white">Crescimento Mensal</h3>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500" />
                        <span class="text-xs text-neutral-600 dark:text-neutral-400">Usuários</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-teal-400" />
                        <span class="text-xs text-neutral-600 dark:text-neutral-400">Treinadores</span>
                    </div>
                </div>
            </div>
            <LineChart :data="monthlyGrowth" :height="220" />
        </div>

        <!-- Platform Metrics -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Métricas da Plataforma</h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="metric in platformMetrics"
                    :key="metric.label"
                    class="group rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 p-4 hover:border-emerald-300 dark:hover:border-emerald-600 transition-all duration-200"
                >
                    <div class="flex items-center gap-2 mb-2">
                        <Server v-if="metric.label === 'Uptime'" class="w-4 h-4 text-emerald-500" />
                        <Zap v-else-if="metric.label === 'Tempo de Resposta'" class="w-4 h-4 text-emerald-500" />
                        <Database v-else-if="metric.label === 'Armazenamento'" class="w-4 h-4 text-emerald-500" />
                        <Activity v-else class="w-4 h-4 text-emerald-500" />
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ metric.label }}</p>
                    </div>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ metric.value }}</p>
                    <div class="flex items-center gap-1 mt-1">
                        <ArrowUpRight v-if="metric.trend === 'up'" class="w-3 h-3 text-emerald-500" />
                        <ArrowDownRight v-else class="w-3 h-3 text-emerald-500" />
                        <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            {{ metric.change }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Recent Users -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Usuários Recentes</h3>
                    <Link href="/users" class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium">
                        Ver todos →
                    </Link>
                </div>
                <div class="space-y-1">
                    <div
                        v-for="recentUser in recentUsers"
                        :key="recentUser.id"
                        class="flex items-center justify-between py-3 px-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                {{ recentUser.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ recentUser.name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ recentUser.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                                {{ recentUser.role }}
                            </span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ recentUser.created_at }}</span>
                        </div>
                    </div>
                    <div v-if="!recentUsers.length" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                        Nenhum usuário cadastrado
                    </div>
                </div>
            </div>

            <!-- Top Trainers -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Top Treinadores</h3>
                <div class="space-y-1">
                    <div
                        v-for="(trainer, index) in topTrainers"
                        :key="index"
                        class="flex items-center justify-between py-3 px-2 rounded-lg hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                {{ trainer.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ trainer.name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ trainer.specialty }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1">
                                <Star class="w-3 h-3 text-amber-500 fill-amber-500" />
                                <span class="text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ trainer.rating }}</span>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                                {{ trainer.clients }} clientes
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
