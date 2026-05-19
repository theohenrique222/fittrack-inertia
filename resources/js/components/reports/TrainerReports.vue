<script setup lang="ts">
import { computed } from 'vue';
import { Users, UserCheck, ClipboardList, Dumbbell, TrendingUp, TrendingDown, Activity, Timer } from 'lucide-vue-next';
import BarChart from '@/components/dashboard/BarChart.vue';
import DonutChart from '@/components/dashboard/DonutChart.vue';
import LineChart from '@/components/dashboard/LineChart.vue';
import DataTable from '@/components/reports/DataTable.vue';

interface Props {
    overview: {
        totalStudents: number;
        activeStudents: number;
        totalWorkouts: number;
        exercisesUsed: number;
        newStudents: number;
        newStudentsChange: number;
        newWorkouts: number;
        newWorkoutsChange: number;
    };
    studentsByStatus: { name: string; value: number; color: string }[];
    workoutsOverTime: { date: string; count: number }[];
    mostUsedExercises: { name: string; muscleGroup: string; count: number }[];
    exercisesByMuscleGroup: { name: string; value: number; color: string }[];
    workoutVolume: {
        totalSets: number;
        totalReps: number;
        totalRestMinutes: number;
        totalVolume: number;
    };
    studentsTable: {
        id: number;
        name: string;
        nickname: string | null;
        email: string;
        hasActiveWorkouts: boolean;
        createdAt: string;
    }[];
}

const props = defineProps<Props>();

const barChartData = computed(() =>
    props.mostUsedExercises.slice(0, 8).map((e) => ({
        day: e.name.length > 12 ? e.name.substring(0, 12) + '...' : e.name,
        value: e.count,
    }))
);

const lineChartData = computed(() =>
    props.workoutsOverTime.map((r) => ({
        month: r.date,
        students: r.count,
        workouts: 0,
    }))
);

const studentColumns = [
    { key: 'name', label: 'Nome', sortable: true },
    { key: 'nickname', label: 'Apelido', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'status', label: 'Status', sortable: false },
    { key: 'createdAt', label: 'Vínculo desde', sortable: true },
];

const studentRows = computed(() =>
    props.studentsTable.map((s) => ({
        ...s,
        status: s.hasActiveWorkouts ? 'Ativo' : 'Inativo',
    }))
);

const statCards = computed(() => [
    {
        label: 'Total de Alunos',
        value: props.overview.totalStudents,
        icon: Users,
        change: props.overview.newStudentsChange,
        sublabel: `${props.overview.newStudents} novos`,
    },
    {
        label: 'Alunos Ativos',
        value: props.overview.activeStudents,
        icon: UserCheck,
        change: 0,
        sublabel: `${props.overview.totalStudents - props.overview.activeStudents} inativos`,
    },
    {
        label: 'Total de Treinos',
        value: props.overview.totalWorkouts,
        icon: ClipboardList,
        change: props.overview.newWorkoutsChange,
        sublabel: `${props.overview.newWorkouts} novos`,
    },
    {
        label: 'Exercícios Utilizados',
        value: props.overview.exercisesUsed,
        icon: Dumbbell,
        change: 0,
        sublabel: 'Exercícios distintos',
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
                        <div v-if="card.change !== 0" class="flex items-center gap-1 mt-2">
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

        <!-- Workout Volume -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <Activity class="w-4 h-4 text-emerald-500" />
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total de Séries</p>
                </div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume.totalSets.toLocaleString('pt-BR') }}</p>
            </div>
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <Activity class="w-4 h-4 text-emerald-500" />
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Total de Repetições</p>
                </div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume.totalReps.toLocaleString('pt-BR') }}</p>
            </div>
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <Timer class="w-4 h-4 text-emerald-500" />
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Descanso Total</p>
                </div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume.totalRestMinutes.toLocaleString('pt-BR') }} min</p>
            </div>
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <Dumbbell class="w-4 h-4 text-emerald-500" />
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Volume Total</p>
                </div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume.totalVolume.toLocaleString('pt-BR') }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Séries × Repetições</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Students by Status -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Alunos por Status</h3>
                <DonutChart :data="studentsByStatus" :size="160" />
            </div>

            <!-- Workouts Over Time -->
            <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Treinos Criados ao Longo do Tempo</h3>
                <LineChart v-if="workoutsOverTime.length" :data="lineChartData" :height="180" />
                <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                    Sem dados para o período selecionado
                </div>
            </div>
        </div>

        <!-- Most Used Exercises -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Exercícios Mais Utilizados nos Meus Treinos</h3>
            <BarChart v-if="barChartData.length" :data="barChartData" :height="180" />
            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                Sem dados para o período selecionado
            </div>
        </div>

        <!-- Muscle Group Distribution -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Distribuição por Grupo Muscular (Meus Treinos)</h3>
            <DonutChart v-if="exercisesByMuscleGroup.length" :data="exercisesByMuscleGroup" :size="160" />
            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                Sem dados disponíveis
            </div>
        </div>

        <!-- Students Table -->
        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
            <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                <h3 class="font-semibold text-neutral-900 dark:text-white">Meus Alunos</h3>
            </div>
            <DataTable :columns="studentColumns" :rows="studentRows" empty-message="Nenhum aluno vinculado">
                <template #cell-status="{ value }">
                    <span
                        :class="[
                            'text-xs px-2 py-1 rounded-full font-medium',
                            value === 'Ativo'
                                ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'
                                : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                        ]"
                    >
                        {{ value }}
                    </span>
                </template>
            </DataTable>
        </div>
    </div>
</template>
