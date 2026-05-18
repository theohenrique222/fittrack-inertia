<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';
import AdminDashboard from '@/components/dashboard/AdminDashboard.vue';
import TrainerDashboard from '@/components/dashboard/TrainerDashboard.vue';
import ClientDashboard from '@/components/dashboard/ClientDashboard.vue';

const page = usePage();

const user = computed(() => page.props.auth.user);

const props = defineProps<{
    stats?: {
        totalUsers?: number;
        totalClients?: number;
        totalTrainers?: number;
        totalExercises?: number;
        activeClients?: number;
        totalCategories?: number;
        totalWorkouts?: number;
        completedWorkouts?: number;
        currentStreak?: number;
    };
    usersByRole?: { role: string; count: number; color: string }[];
    monthlyGrowth?: { month: string; users?: number; trainers?: number; clients?: number; workouts?: number }[];
    systemActivity?: { day: string; logins: number; registrations: number }[];
    recentUsers?: { id: number; name: string; email: string; role: string; created_at: string | null }[];
    topTrainers?: { name: string; clients: number; specialty: string; rating: number }[];
    platformMetrics?: { label: string; value: string; change: string; trend: string }[];
    recentClients?: {
        id: number;
        name: string;
        email: string;
        nickname: string | null;
        created_at: string | null;
    }[];
    weeklyActivity?: { day: string; value: number }[];
    muscleGroupDistribution?: { name: string; value: number; color: string }[];
    upcomingWorkouts?: any[];
    quickActions?: { label: string; route: string; icon: string }[];
    weeklyWorkouts?: { day: string; completed: boolean; type: string }[];
    progressData?: { week: string; weight: number; bodyFat: number }[];
    nutritionData?: {
        calories: { consumed: number; target: number; percentage: number };
        protein: { consumed: number; target: number; percentage: number; unit: string };
        carbs: { consumed: number; target: number; percentage: number; unit: string };
        fat: { consumed: number; target: number; percentage: number; unit: string };
    };
    bodyMetrics?: { label: string; value: string; change: string; trend: string }[];
    recentAchievements?: { title: string; description: string; icon: string; date: string }[];
    trainer?: { name: string; specialty: string; email: string };
}>();

const userRole = computed(() => user.value?.role);

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

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 overflow-x-auto">
        <AdminDashboard
            v-if="userRole === 'admin'"
            :stats="{
                totalUsers: stats?.totalUsers ?? 0,
                totalClients: stats?.totalClients ?? 0,
                totalTrainers: stats?.totalTrainers ?? 0,
                totalExercises: stats?.totalExercises ?? 0,
            }"
            :users-by-role="usersByRole ?? []"
            :monthly-growth="(monthlyGrowth ?? []).map(m => ({ month: m.month, users: m.users ?? 0, trainers: m.trainers ?? 0 }))"
            :system-activity="systemActivity ?? []"
            :recent-users="recentUsers ?? []"
            :top-trainers="topTrainers ?? []"
            :platform-metrics="platformMetrics ?? []"
            :quick-actions="quickActions ?? []"
        />

        <TrainerDashboard
            v-else-if="userRole === 'personal' || userRole === 'self'"
            :stats="{
                totalClients: stats?.totalClients ?? 0,
                activeClients: stats?.activeClients ?? 0,
                totalExercises: stats?.totalExercises ?? 0,
                totalCategories: stats?.totalCategories ?? 0,
            }"
            :recent-clients="recentClients ?? []"
            :weekly-activity="weeklyActivity ?? []"
            :muscle-group-distribution="muscleGroupDistribution ?? []"
            :monthly-growth="(monthlyGrowth ?? []).map(m => ({ month: m.month, clients: m.clients ?? 0, workouts: m.workouts ?? 0 }))"
            :upcoming-workouts="upcomingWorkouts ?? []"
            :quick-actions="quickActions ?? []"
        />

        <ClientDashboard
            v-else
            :stats="{
                totalWorkouts: stats?.totalWorkouts ?? 0,
                completedWorkouts: stats?.completedWorkouts ?? 0,
                currentStreak: stats?.currentStreak ?? 0,
                totalExercises: stats?.totalExercises ?? 0,
            }"
            :weekly-workouts="weeklyWorkouts ?? []"
            :progress-data="progressData ?? []"
            :nutrition-data="nutritionData ?? {
                calories: { consumed: 0, target: 0, percentage: 0 },
                protein: { consumed: 0, target: 0, percentage: 0, unit: 'g' },
                carbs: { consumed: 0, target: 0, percentage: 0, unit: 'g' },
                fat: { consumed: 0, target: 0, percentage: 0, unit: 'g' },
            }"
            :body-metrics="bodyMetrics ?? []"
            :upcoming-workouts="upcomingWorkouts ?? []"
            :recent-achievements="recentAchievements ?? []"
            :trainer="trainer ?? { name: 'Sem treinador', specialty: '', email: '' }"
        />
    </div>
</template>
