<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminDashboard from '@/components/dashboard/AdminDashboard.vue';
import ClientDashboard from '@/components/dashboard/ClientDashboard.vue';
import TrainerDashboard from '@/components/dashboard/TrainerDashboard.vue';
import { dashboard } from '@/routes';

const page = usePage();

const user = computed(() => page.props.auth.user);

defineProps<{
    stats?: {
        totalUsers?: number;
        totalStudents?: number;
        totalTrainers?: number;
        totalExercises?: number;
        activeStudents?: number;
        totalCategories?: number;
        totalWorkouts?: number;
        completedWorkouts?: number;
        currentStreak?: number;
    };
    usersByRole?: { role: string; count: number; color: string }[];
    monthlyGrowth?: { month: string; users?: number; trainers?: number; students?: number; workouts?: number }[];
    systemActivity?: { day: string; logins: number; registrations: number }[];
    recentUsers?: { id: number; name: string; email: string; role: string; created_at: string | null }[];
    topTrainers?: { name: string; students: number; specialty: string; rating: number }[];
    platformMetrics?: { label: string; value: string; change: string; trend: string }[];
    recentStudents?: {
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
    activeWorkout?: {
        id: number;
        name: string;
        description?: string;
        exercises_count: number;
        total_reps: number;
        estimated_time_minutes: number;
        is_active: boolean;
        exercises?: {
            id: number;
            name: string;
            category?: string | null;
            pivot: {
                sets: number;
                reps: number;
                rest_seconds: number;
            };
        }[];
    } | null;
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
    completedWorkouts?: { id: number; name: string; exercises: number; completed_at: string }[];
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

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <AdminDashboard
            v-if="userRole === 'admin'"
            :stats="{
                totalUsers: stats?.totalUsers ?? 0,
                totalStudents: stats?.totalStudents ?? 0,
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
                totalStudents: stats?.totalStudents ?? 0,
                activeStudents: stats?.activeStudents ?? 0,
                totalExercises: stats?.totalExercises ?? 0,
                totalCategories: stats?.totalCategories ?? 0,
            }"
            :recent-students="recentStudents ?? []"
            :weekly-activity="weeklyActivity ?? []"
            :muscle-group-distribution="muscleGroupDistribution ?? []"
            :monthly-growth="(monthlyGrowth ?? []).map(m => ({ month: m.month, students: m.students ?? 0, workouts: m.workouts ?? 0 }))"
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
            :active-workout="activeWorkout ?? null"
            :weekly-workouts="weeklyWorkouts ?? []"
            :progress-data="progressData ?? []"
            :nutrition-data="nutritionData ?? {
                calories: { consumed: 0, target: 0, percentage: 0 },
                protein: { consumed: 0, target: 0, percentage: 0, unit: 'g' },
                carbs: { consumed: 0, target: 0, percentage: 0, unit: 'g' },
                fat: { consumed: 0, target: 0, percentage: 0, unit: 'g' },
            }"
            :body-metrics="bodyMetrics ?? []"
            :completed-workouts="completedWorkouts ?? []"
            :upcoming-workouts="upcomingWorkouts ?? []"
            :recent-achievements="recentAchievements ?? []"
            :trainer="trainer ?? { name: 'Sem treinador', specialty: '', email: '' }"
        />
    </div>
</template>
