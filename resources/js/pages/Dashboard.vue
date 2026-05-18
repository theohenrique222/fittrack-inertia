<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';
import TrainerDashboard from '@/components/dashboard/TrainerDashboard.vue';
import ClientDashboard from '@/components/dashboard/ClientDashboard.vue';

const page = usePage();

const user = computed(() => page.props.auth.user);

const props = defineProps<{
    stats?: {
        totalClients?: number;
        activeClients?: number;
        totalExercises?: number;
        totalCategories?: number;
        totalWorkouts?: number;
        completedWorkouts?: number;
        currentStreak?: number;
    };
    recentClients?: {
        id: number;
        name: string;
        email: string;
        nickname: string | null;
        created_at: string | null;
    }[];
    weeklyActivity?: { day: string; value: number }[];
    muscleGroupDistribution?: { name: string; value: number; color: string }[];
    monthlyGrowth?: { month: string; clients: number; workouts: number }[];
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

const isTrainer = computed(() => {
    return user.value?.role === 'personal' || user.value?.role === 'admin' || user.value?.role === 'self';
});

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
        <TrainerDashboard
            v-if="isTrainer"
            :stats="{
                totalClients: stats?.totalClients ?? 0,
                activeClients: stats?.activeClients ?? 0,
                totalExercises: stats?.totalExercises ?? 0,
                totalCategories: stats?.totalCategories ?? 0,
            }"
            :recent-clients="recentClients ?? []"
            :weekly-activity="weeklyActivity ?? []"
            :muscle-group-distribution="muscleGroupDistribution ?? []"
            :monthly-growth="monthlyGrowth ?? []"
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
