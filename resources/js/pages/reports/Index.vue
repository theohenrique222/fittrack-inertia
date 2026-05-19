<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { BarChart3, LayoutGrid, Users, Dumbbell, ClipboardList } from 'lucide-vue-next';
import PeriodFilter from '@/components/reports/PeriodFilter.vue';
import AdminReports from '@/components/reports/AdminReports.vue';
import TrainerReports from '@/components/reports/TrainerReports.vue';
import { reports } from '@/routes';

const page = usePage();
const userRole = computed(() => page.props.auth.user.role);

const tabs = computed(() => {
    if (userRole.value === 'admin') {
        return [
            { key: 'overview', label: 'Visão Geral', icon: LayoutGrid },
            { key: 'users', label: 'Usuários', icon: Users },
            { key: 'workouts', label: 'Treinos', icon: ClipboardList },
            { key: 'exercises', label: 'Exercícios', icon: Dumbbell },
        ];
    }

    return [
        { key: 'overview', label: 'Visão Geral', icon: LayoutGrid },
        { key: 'students', label: 'Alunos', icon: Users },
        { key: 'workouts', label: 'Treinos', icon: ClipboardList },
        { key: 'exercises', label: 'Exercícios', icon: Dumbbell },
    ];
});

const activeTab = ref('overview');
const selectedPeriod = ref(props.selectedPeriod);

function handlePeriodChange(period: string) {
    selectedPeriod.value = period;
    router.get(reports(), { period }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

const props = defineProps<{
    overview?: {
        totalUsers?: number;
        totalClients?: number;
        totalTrainers?: number;
        totalExercises?: number;
        totalWorkouts?: number;
        newUsers?: number;
        newUsersChange?: number;
        newClients?: number;
        newClientsChange?: number;
        newTrainers?: number;
        newTrainersChange?: number;
        newWorkouts?: number;
        newWorkoutsChange?: number;
        totalStudents?: number;
        activeStudents?: number;
        exercisesUsed?: number;
        newStudents?: number;
        newStudentsChange?: number;
    };
    usersByRole?: { role: string; count: number; color: string }[];
    registrationsOverTime?: { date: string; count: number }[];
    topTrainers?: { name: string; specialty: string; studentsCount: number }[];
    mostUsedExercises?: { name: string; muscleGroup: string; count: number }[];
    exercisesByMuscleGroup?: { name: string; value: number; color: string }[];
    workoutsByTrainer?: { trainer: string; count: number }[];
    usersTable?: { id: number; name: string; email: string; role: string; createdAt: string }[];
    studentsByStatus?: { name: string; value: number; color: string }[];
    workoutsOverTime?: { date: string; count: number }[];
    workoutVolume?: {
        totalSets: number;
        totalReps: number;
        totalRestMinutes: number;
        totalVolume: number;
    };
    studentsTable?: {
        id: number;
        name: string;
        nickname: string | null;
        email: string;
        hasActiveWorkouts: boolean;
        createdAt: string;
    }[];
    selectedPeriod: string;
}>();

const isAdmin = computed(() => userRole.value === 'admin');

const adminOverview = computed(() => ({
    totalUsers: props.overview?.totalUsers ?? 0,
    totalClients: props.overview?.totalClients ?? 0,
    totalTrainers: props.overview?.totalTrainers ?? 0,
    totalExercises: props.overview?.totalExercises ?? 0,
    totalWorkouts: props.overview?.totalWorkouts ?? 0,
    newUsers: props.overview?.newUsers ?? 0,
    newUsersChange: props.overview?.newUsersChange ?? 0,
    newClients: props.overview?.newClients ?? 0,
    newClientsChange: props.overview?.newClientsChange ?? 0,
    newTrainers: props.overview?.newTrainers ?? 0,
    newTrainersChange: props.overview?.newTrainersChange ?? 0,
    newWorkouts: props.overview?.newWorkouts ?? 0,
    newWorkoutsChange: props.overview?.newWorkoutsChange ?? 0,
}));

const trainerOverview = computed(() => ({
    totalStudents: props.overview?.totalStudents ?? 0,
    activeStudents: props.overview?.activeStudents ?? 0,
    totalWorkouts: props.overview?.totalWorkouts ?? 0,
    exercisesUsed: props.overview?.exercisesUsed ?? 0,
    newStudents: props.overview?.newStudents ?? 0,
    newStudentsChange: props.overview?.newStudentsChange ?? 0,
    newWorkouts: props.overview?.newWorkouts ?? 0,
    newWorkoutsChange: props.overview?.newWorkoutsChange ?? 0,
}));
</script>

<template>
    <Head title="Relatórios" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 md:p-8 text-white shadow-xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-32 translate-x-32" />
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24" />
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <BarChart3 class="w-8 h-8" />
                        <h1 class="text-2xl md:text-3xl font-bold">
                            Relatórios
                        </h1>
                    </div>
                    <p class="text-sm md:text-base opacity-90 mt-1 max-w-xl">
                        Análise detalhada dos dados da plataforma com insights e métricas
                    </p>
                </div>
                <PeriodFilter :selected="selectedPeriod" @change="handlePeriodChange" />
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-1 border-b border-neutral-200 dark:border-neutral-700">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                    'flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 transition-all duration-200',
                    activeTab === tab.key
                        ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400'
                        : 'border-transparent text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 hover:border-neutral-300',
                ]"
            >
                <component :is="tab.icon" class="w-4 h-4" />
                {{ tab.label }}
            </button>
        </div>

        <!-- Admin Reports -->
        <template v-if="isAdmin">
            <AdminReports
                v-if="activeTab === 'overview'"
                :overview="adminOverview"
                :users-by-role="usersByRole ?? []"
                :registrations-over-time="registrationsOverTime ?? []"
                :top-trainers="topTrainers ?? []"
                :most-used-exercises="mostUsedExercises ?? []"
                :exercises-by-muscle-group="exercisesByMuscleGroup ?? []"
                :workouts-by-trainer="workoutsByTrainer ?? []"
                :users-table="usersTable ?? []"
            />

            <div v-if="activeTab === 'users'" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Todos os Usuários</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Listagem completa de usuários da plataforma</p>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                        <div
                            v-for="role in usersByRole"
                            :key="role.role"
                            class="rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 p-4"
                        >
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: role.color }" />
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ role.role }}</p>
                            </div>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ role.count }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'workouts'" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Relatório de Treinos</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Análise de treinos criados por treinador</p>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 mb-6">
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Treinos por Treinador</h3>
                            <div class="space-y-3">
                                <div
                                    v-for="(item, index) in workoutsByTrainer"
                                    :key="index"
                                    class="flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white font-semibold text-xs">
                                            {{ item.trainer.charAt(0).toUpperCase() }}
                                        </div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ item.trainer }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ item.count }} treinos</span>
                                </div>
                                <div v-if="!workoutsByTrainer?.length" class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                    Sem dados para o período selecionado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'exercises'" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Relatório de Exercícios</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Análise de exercícios por grupo muscular e uso</p>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 lg:grid-cols-2 mb-6">
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Exercícios por Grupo Muscular</h3>
                            <DonutChart v-if="exercisesByMuscleGroup?.length" :data="exercisesByMuscleGroup" :size="160" />
                            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                Sem dados disponíveis
                            </div>
                        </div>
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Exercícios Mais Utilizados</h3>
                            <BarChart v-if="mostUsedExercises?.length" :data="mostUsedExercises.slice(0, 8).map(e => ({ day: e.name.substring(0, 12), value: e.count }))" :height="180" />
                            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                Sem dados para o período selecionado
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Trainer Reports -->
        <template v-else>
            <TrainerReports
                v-if="activeTab === 'overview'"
                :overview="trainerOverview"
                :students-by-status="studentsByStatus ?? []"
                :workouts-over-time="workoutsOverTime ?? []"
                :most-used-exercises="mostUsedExercises ?? []"
                :exercises-by-muscle-group="exercisesByMuscleGroup ?? []"
                :workout-volume="workoutVolume ?? { totalSets: 0, totalReps: 0, totalRestMinutes: 0, totalVolume: 0 }"
                :students-table="studentsTable ?? []"
            />

            <div v-if="activeTab === 'students'" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Meus Alunos</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Listagem completa dos seus alunos</p>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 sm:grid-cols-2 mb-6">
                        <div
                            v-for="status in studentsByStatus"
                            :key="status.name"
                            class="rounded-lg border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800 p-4"
                        >
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: status.color }" />
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">{{ status.name }}</p>
                            </div>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ status.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'workouts'" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Meus Treinos</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Análise dos treinos criados</p>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 lg:grid-cols-2 mb-6">
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Treinos ao Longo do Tempo</h3>
                            <LineChart v-if="workoutsOverTime?.length" :data="workoutsOverTime.map(r => ({ month: r.date, students: r.count, workouts: 0 }))" :height="180" />
                            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                Sem dados para o período selecionado
                            </div>
                        </div>
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Volume de Treino</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Séries</p>
                                    <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume?.totalSets ?? 0 }}</p>
                                </div>
                                <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Repetições</p>
                                    <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume?.totalReps ?? 0 }}</p>
                                </div>
                                <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Descanso</p>
                                    <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume?.totalRestMinutes ?? 0 }} min</p>
                                </div>
                                <div class="rounded-lg bg-neutral-50 dark:bg-neutral-800 p-4">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Volume</p>
                                    <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ workoutVolume?.totalVolume ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'exercises'" class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 shadow-sm">
                <div class="px-5 py-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Meus Exercícios</h3>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Análise dos exercícios utilizados nos treinos</p>
                </div>
                <div class="p-5">
                    <div class="grid gap-4 lg:grid-cols-2 mb-6">
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Distribuição por Grupo Muscular</h3>
                            <DonutChart v-if="exercisesByMuscleGroup?.length" :data="exercisesByMuscleGroup" :size="160" />
                            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                Sem dados disponíveis
                            </div>
                        </div>
                        <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-white dark:bg-neutral-900 p-5 shadow-sm">
                            <h3 class="font-semibold mb-4 text-neutral-900 dark:text-white">Exercícios Mais Utilizados</h3>
                            <BarChart v-if="mostUsedExercises?.length" :data="mostUsedExercises.slice(0, 8).map(e => ({ day: e.name.substring(0, 12), value: e.count }))" :height="180" />
                            <div v-else class="py-8 text-center text-neutral-500 dark:text-neutral-400">
                                Sem dados para o período selecionado
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
