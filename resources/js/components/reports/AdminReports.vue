<script setup lang="ts">
import { computed } from 'vue';
import { Users, UserPlus, UserCheck, Dumbbell, ClipboardList, TrendingUp, TrendingDown, Star, Award } from 'lucide-vue-next';
import BarChart from '@/components/dashboard/BarChart.vue';
import DonutChart from '@/components/dashboard/DonutChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import DataTable from '@/components/reports/DataTable.vue';

interface Props {
    overview: {
        totalUsers: number;
        totalClients: number;
        totalTrainers: number;
        totalExercises: number;
        totalWorkouts: number;
        newUsers: number;
        newUsersChange: number;
        newClients: number;
        newClientsChange: number;
        newTrainers: number;
        newTrainersChange: number;
        newWorkouts: number;
        newWorkoutsChange: number;
    };
    usersByRole: { role: string; count: number; color: string }[];
    registrationsOverTime: { date: string; count: number }[];
    topTrainers: { name: string; specialty: string; studentsCount: number }[];
    mostUsedExercises: { name: string; muscleGroup: string; count: number }[];
    exercisesByMuscleGroup: { name: string; value: number; color: string }[];
    workoutsByTrainer: { trainer: string; count: number }[];
    usersTable: { id: number; name: string; email: string; role: string; createdAt: string }[];
}

const props = defineProps<Props>();

const barChartData = computed(() =>
    props.mostUsedExercises.slice(0, 8).map((e) => ({
        day: e.name.length > 12 ? e.name.substring(0, 12) + '...' : e.name,
        value: e.count,
    }))
);

const lineChartData = computed(() =>
    props.registrationsOverTime.map((r) => ({
        month: r.date,
        students: r.count,
        workouts: 0,
    }))
);

const workoutsBarData = computed(() =>
    props.workoutsByTrainer.slice(0, 8).map((w) => ({
        day: w.trainer.split(' ')[0],
        value: w.count,
    }))
);

const userColumns = [
    { key: 'name', label: 'Nome', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Perfil', sortable: true },
    { key: 'createdAt', label: 'Criado em', sortable: true },
];

const statCards = computed(() => [
    {
        label: 'Total de Usuários',
        value: props.overview.totalUsers,
        icon: Users,
        change: props.overview.newUsersChange,
        sublabel: `${props.overview.newUsers} novos`,
    },
    {
        label: 'Total de Alunos',
        value: props.overview.totalClients,
        icon: UserPlus,
        change: props.overview.newClientsChange,
        sublabel: `${props.overview.newClients} novos`,
    },
    {
        label: 'Total de Treinadores',
        value: props.overview.totalTrainers,
        icon: UserCheck,
        change: props.overview.newTrainersChange,
        sublabel: `${props.overview.newTrainers} novos`,
    },
    {
        label: 'Total de Treinos',
        value: props.overview.totalWorkouts,
        icon: ClipboardList,
        change: props.overview.newWorkoutsChange,
        sublabel: `${props.overview.newWorkouts} novos`,
    },
]);
</script>

<template>
    <div class="flex flex-col gap-6">
        <!-- Stat Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="card in statCards"
                :key="card.label"
                class="group rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-700 transition-all duration-300"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">{{ card.label }}</p>
                        <h2 class="text-3xl font-bold mt-2 text-neutral-900 dark:text-white">{{ card.value }}</h2>
                        <div class="flex items-center gap-1 mt-2">
                            <TrendingUp v-if="card.change >= 0" class="w-4 h-4 text-emerald-500" />
                            <TrendingDown v-else class="w-4 h-4 text-red-500" />
                            <span :class="['text-xs font-medium', card.change >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400']">
                                {{ card.change >= 0 ? '+' : '' }}{{ card.change }}%
                            </span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">vs período anterior</span>
                        </div>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">{{ card.sublabel }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <component :is="card.icon" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Exercises Overview -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <Dumbbell class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <h3 class="font-semibold text-neutral-900 dark:text-white">Exercícios Disponíveis</h3>
            </div>
            <div class="text-3xl font-bold text-neutral-900 dark:text-white">{{ overview.totalExercises }}</div>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Exercícios ativos no catálogo</p>
        </div>

        <!-- Charts Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Users by Role -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Distribuição por Perfil</h3>
                <DonutChart :data="usersByRole" :size="160" />
            </div>

            <!-- Registrations Over Time -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Cadastros ao Longo do Tempo</h3>
                <LineChart v-if="registrationsOverTime.length" :data="lineChartData" :height="180" />
                <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                    Sem dados para o período selecionado
                </div>
            </div>
        </div>

        <!-- Most Used Exercises -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Exercícios Mais Utilizados nos Treinos</h3>
            <BarChart v-if="barChartData.length" :data="barChartData" :height="180" />
            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                Sem dados para o período selecionado
            </div>
        </div>

        <!-- Muscle Group Distribution + Workouts by Trainer -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Muscle Group Distribution -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <Award class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Exercícios por Grupo Muscular</h3>
                </div>
                <DonutChart v-if="exercisesByMuscleGroup.length" :data="exercisesByMuscleGroup" :size="160" />
                <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                    Sem dados disponíveis
                </div>
            </div>

            <!-- Workouts by Trainer -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Treinos por Treinador</h3>
                <BarChart v-if="workoutsBarData.length" :data="workoutsBarData" :height="180" />
                <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                    Sem dados para o período selecionado
                </div>
            </div>
        </div>

        <!-- Top Trainers -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <Star class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                <h3 class="font-semibold text-neutral-900 dark:text-white">Top Treinadores por Nº de Alunos</h3>
            </div>
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
                    <span class="text-xs px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                        {{ trainer.studentsCount }} alunos
                    </span>
                </div>
                <div v-if="!topTrainers.length" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                    Nenhum treinador cadastrado
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
            <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <h3 class="font-semibold text-neutral-900 dark:text-white">Usuários Recentes</h3>
            </div>
            <DataTable :columns="userColumns" :rows="usersTable" empty-message="Nenhum usuário encontrado">
                <template #cell-role="{ value }">
                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 font-medium">
                        {{ value }}
                    </span>
                </template>
            </DataTable>
        </div>
    </div>
</template>
