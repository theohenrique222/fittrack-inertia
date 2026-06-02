<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            { title: 'Medidas Corporais', href: '/measurements' },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Activity,
    ChevronRight,
    Dumbbell,
    Ruler,
    Scale,
    Search,
    TrendingUp,
    Weight,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';

const page = usePage();
const { toasts, success, error } = useToast();

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) success(flash.success);
        if (flash?.error) error(flash.error);
    },
);

interface StudentMeasurement {
    id: number;
    name: string;
    nickname: string | null;
    latest_measurement: {
        id: number;
        recorded_at: string;
        weight: number;
        height: number;
        metrics: {
            bmi: { value: number; classification: string; color: string };
            body_fat: { value: number; classification: string; color: string };
            lean_mass: number;
            fat_mass: number;
            bmr: number;
            tdee: number;
            daily_water: number;
        };
    } | null;
}

const props = defineProps<{
    title: string;
    students: StudentMeasurement[];
}>();

const searchQuery = ref('');

const filteredStudents = computed(() => {
    if (!searchQuery.value) return props.students;

    const q = searchQuery.value.toLowerCase();
    return props.students.filter(
        (s) =>
            s.name.toLowerCase().includes(q) ||
            (s.nickname?.toLowerCase().includes(q) ?? false),
    );
});

const avatarColors = [
    'from-emerald-400 to-emerald-600',
    'from-blue-400 to-blue-600',
    'from-violet-400 to-violet-600',
    'from-orange-400 to-orange-600',
    'from-pink-400 to-pink-600',
    'from-cyan-400 to-cyan-600',
    'from-rose-400 to-rose-600',
    'from-amber-400 to-amber-600',
];

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

const statsSummary = computed(() => {
    const total = props.students.length;
    const withData = props.students.filter((s) => s.latest_measurement).length;
    const withoutData = total - withData;
    return { total, withData, withoutData };
});
</script>

<template>
    <Head :title="title" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <!-- Header -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 p-6 dark:from-neutral-800 dark:via-neutral-700 dark:to-neutral-800"
        >
            <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="relative">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg"
                    >
                        <Ruler class="h-7 w-7 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Medidas Corporais</h1>
                        <p class="text-sm text-neutral-300">
                            {{ statsSummary.total }} alunos
                            <template v-if="statsSummary.withData > 0">
                                · {{ statsSummary.withData }} com medições
                            </template>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div
                class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30"
                    >
                        <Weight class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Média de Peso</p>
                        <p class="text-xl font-bold text-neutral-900 dark:text-white">
                            <template v-if="statsSummary.withData > 0">
                                {{
                                    (
                                        props.students
                                            .filter((s) => s.latest_measurement)
                                            .reduce((acc, s) => acc + s.latest_measurement!.weight, 0) /
                                        statsSummary.withData
                                    ).toFixed(1)
                                }}
                                kg
                            </template>
                            <template v-else>—</template>
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30"
                    >
                        <TrendingUp class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Média IMC</p>
                        <p class="text-xl font-bold text-neutral-900 dark:text-white">
                            <template v-if="statsSummary.withData > 0">
                                {{
                                    (
                                        props.students
                                            .filter((s) => s.latest_measurement?.metrics)
                                            .reduce((acc, s) => acc + s.latest_measurement!.metrics!.bmi.value, 0) /
                                        statsSummary.withData
                                    ).toFixed(1)
                                }}
                            </template>
                            <template v-else>—</template>
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900/30"
                    >
                        <Activity class="h-5 w-5 text-rose-600 dark:text-rose-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">% Gordura Média</p>
                        <p class="text-xl font-bold text-neutral-900 dark:text-white">
                            <template v-if="statsSummary.withData > 0">
                                {{
                                    (
                                        props.students
                                            .filter((s) => s.latest_measurement?.metrics)
                                            .reduce(
                                                (acc, s) => acc + s.latest_measurement!.metrics!.body_fat.value,
                                                0,
                                            ) / statsSummary.withData
                                    ).toFixed(1)
                                }}%
                            </template>
                            <template v-else>—</template>
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
            >
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30"
                    >
                        <Search class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Total Alunos</p>
                        <p class="text-xl font-bold text-neutral-900 dark:text-white">
                            {{ statsSummary.total }}
                            <span class="text-sm font-normal text-neutral-400">
                                · {{ statsSummary.withoutData }} sem dados
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400" />
            <Input
                v-model="searchQuery"
                placeholder="Buscar aluno por nome..."
                class="pl-9 bg-white dark:bg-neutral-800 border-neutral-200 dark:border-neutral-700"
            />
        </div>

        <!-- Students Grid -->
        <div v-if="filteredStudents.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="student in filteredStudents"
                :key="student.id"
                class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                @click="router.visit(`/students/${student.id}/measurements`)"
            >
                <!-- Card Header -->
                <div
                    class="flex items-center gap-3 border-b border-neutral-100 bg-gradient-to-r from-neutral-50 to-white p-4 dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-800"
                >
                    <div
                        :class="[
                            'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-sm font-bold text-white shadow-sm',
                            getAvatarColor(student.id),
                        ]"
                    >
                        {{ getInitials(student.name) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold text-neutral-900 group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
                            {{ student.name }}
                        </p>
                        <p v-if="student.nickname" class="truncate text-xs text-neutral-400">
                            {{ student.nickname }}
                        </p>
                    </div>
                    <ChevronRight
                        class="h-4 w-4 shrink-0 text-neutral-300 transition-colors group-hover:text-emerald-500 dark:text-neutral-600"
                    />
                </div>

                <!-- Card Body -->
                <div v-if="student.latest_measurement" class="p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <Badge
                            variant="secondary"
                            class="bg-emerald-50 text-xs text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300"
                        >
                            {{ student.latest_measurement.recorded_at }}
                        </Badge>
                        <span class="text-xs text-neutral-400">
                            <template v-if="student.latest_measurement.metrics">
                                TMB {{ student.latest_measurement.metrics.bmr }} kcal
                            </template>
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div
                            class="rounded-lg bg-neutral-50 p-2.5 text-center dark:bg-neutral-700/50"
                        >
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Peso</p>
                            <p class="text-sm font-bold text-neutral-900 dark:text-white">
                                {{ student.latest_measurement.weight }} kg
                            </p>
                        </div>
                        <div
                            class="rounded-lg bg-neutral-50 p-2.5 text-center dark:bg-neutral-700/50"
                        >
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">IMC</p>
                            <p
                                v-if="student.latest_measurement.metrics"
                                class="text-sm font-bold"
                                :style="{ color: student.latest_measurement.metrics.bmi.color }"
                            >
                                {{ student.latest_measurement.metrics.bmi.value }}
                            </p>
                            <p v-else class="text-sm text-neutral-400">—</p>
                        </div>
                        <div
                            class="rounded-lg bg-neutral-50 p-2.5 text-center dark:bg-neutral-700/50"
                        >
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">% Gordura</p>
                            <p
                                v-if="student.latest_measurement.metrics"
                                class="text-sm font-bold"
                                :style="{ color: student.latest_measurement.metrics.body_fat.color }"
                            >
                                {{ student.latest_measurement.metrics.body_fat.value }}%
                            </p>
                            <p v-else class="text-sm text-neutral-400">—</p>
                        </div>
                    </div>

                    <div
                        v-if="student.latest_measurement.metrics"
                        class="mt-3 grid grid-cols-2 gap-2"
                    >
                        <div class="flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                            <Dumbbell class="h-3 w-3 text-emerald-500" />
                            <span>M. Magra: {{ student.latest_measurement.metrics.lean_mass }} kg</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                            <Scale class="h-3 w-3 text-violet-500" />
                            <span>Água: {{ student.latest_measurement.metrics.daily_water }} ml</span>
                        </div>
                    </div>
                </div>

                <div v-else class="flex flex-col items-center gap-2 px-4 py-6 text-center">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700"
                    >
                        <Ruler class="h-5 w-5 text-neutral-400" />
                    </div>
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">
                        Sem medições
                    </p>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500">
                        Clique para registrar
                    </p>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-neutral-300 bg-neutral-50 py-20 text-center dark:border-neutral-700 dark:bg-neutral-900/50"
        >
            <div
                class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-700"
            >
                <Ruler class="h-10 w-10 text-neutral-400" />
            </div>
            <h3 class="text-xl font-bold text-neutral-900 dark:text-white">
                {{ searchQuery ? 'Nenhum aluno encontrado' : 'Nenhuma medição registrada' }}
            </h3>
            <p class="mt-2 max-w-md text-sm text-neutral-500 dark:text-neutral-400">
                {{ searchQuery ? 'Tente buscar com outros termos' : 'Acesse o perfil de um aluno para registrar a primeira medição corporal.' }}
            </p>
            <Button
                v-if="!searchQuery"
                class="mt-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 hover:brightness-110"
                @click="router.visit('/students')"
            >
                Ir para Alunos
            </Button>
        </div>
    </div>

    <ToastContainer v-model="toasts" />
</template>
