<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Activity,
    ArrowLeft,
    Droplets,
    Dumbbell,
    Flame,
    Plus,
    Ruler,
    Scale,
    TrendingUp,
    Zap,
} from 'lucide-vue-next';
import { computed, defineOptions, ref, watch } from 'vue';

defineOptions({
    layout: (pageProps: any) => ({
        breadcrumbs: [
            { title: 'Alunos', href: '/students' },
            { title: pageProps?.student?.name ?? 'Medidas', href: `/students/${pageProps?.student?.id}/measurements` },
        ],
    }),
});
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
import CompleteProfileCard from '@/pages/students/components/CompleteProfileCard.vue';
import CreateBodyMeasurementSheet from '@/pages/students/components/CreateBodyMeasurementSheet.vue';

const page = usePage();
const { toasts, success, error } = useToast();

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
success(flash.success);
}

        if (flash?.error) {
error(flash.error);
}
    },
);

interface Metrics {
    bmi: { value: number; classification: string; color: string };
    body_fat: { value: number; classification: string; color: string };
    lean_mass: number;
    fat_mass: number;
    bmr: number;
    tdee: number;
    daily_water: number;
    macros: {
        calories: number;
        protein: { grams: number; percentage: number };
        carbs: { grams: number; percentage: number };
        fat: { grams: number; percentage: number };
    };
}

interface Measurement {
    id: number;
    recorded_at: string;
    weight: number;
    height: number;
    neck: number | null;
    waist: number | null;
    hip: number | null;
    chest: number | null;
    thigh: number | null;
    arm: number | null;
    forearm: number | null;
    calf: number | null;
    shoulders: number | null;
    activity_level: string;
    goal: string;
    notes: string | null;
    metrics: Metrics | null;
}

interface LatestMeasurement {
    id: number;
    recorded_at: string;
    weight: number;
    height: number;
    metrics: Metrics;
}

const props = defineProps<{
    title: string;
    student: { id: number; name: string };
    measurements: {
        data: Measurement[];
        meta: any;
    };
    latest: LatestMeasurement | null;
    user_has_profile: boolean;
    user_gender: string | null;
    user_birthdate: string | null;
}>();

const isCreateOpen = ref(false);
const isProfileComplete = ref(props.user_has_profile);

function handleProfileSaved() {
    isProfileComplete.value = true;
    router.reload({ preserveScroll: true });
}

const metricCards = computed(() => {
    if (!props.latest) {
return [];
}

    const m = props.latest.metrics;

    return [
        {
            icon: Scale,
            label: 'IMC',
            value: String(m.bmi.value),
            sub: m.bmi.classification,
            color: 'from-blue-500 to-blue-600',
            bg: 'bg-blue-50 dark:bg-blue-900/20',
            iconColor: 'text-blue-600 dark:text-blue-400',
        },
        {
            icon: Activity,
            label: 'Gordura Corporal',
            value: `${m.body_fat.value}%`,
            sub: m.body_fat.classification,
            color: 'from-rose-500 to-rose-600',
            bg: 'bg-rose-50 dark:bg-rose-900/20',
            iconColor: 'text-rose-600 dark:text-rose-400',
        },
        {
            icon: Dumbbell,
            label: 'Massa Magra',
            value: `${m.lean_mass} kg`,
            sub: `Gordura: ${m.fat_mass} kg`,
            color: 'from-emerald-500 to-emerald-600',
            bg: 'bg-emerald-50 dark:bg-emerald-900/20',
            iconColor: 'text-emerald-600 dark:text-emerald-400',
        },
        {
            icon: Flame,
            label: 'TMB',
            value: `${m.bmr} kcal`,
            sub: `TDEE: ${m.tdee} kcal`,
            color: 'from-orange-500 to-orange-600',
            bg: 'bg-orange-50 dark:bg-orange-900/20',
            iconColor: 'text-orange-600 dark:text-orange-400',
        },
        {
            icon: Droplets,
            label: 'Água Diária',
            value: `${m.daily_water} ml`,
            sub: `${(m.daily_water / 1000).toFixed(1)} litros`,
            color: 'from-cyan-500 to-cyan-600',
            bg: 'bg-cyan-50 dark:bg-cyan-900/20',
            iconColor: 'text-cyan-600 dark:text-cyan-400',
        },
        {
            icon: Zap,
            label: 'Calorias Diárias',
            value: `${m.macros.calories} kcal`,
            sub: `P:${m.macros.protein.grams}g C:${m.macros.carbs.grams}g G:${m.macros.fat.grams}g`,
            color: 'from-violet-500 to-violet-600',
            bg: 'bg-violet-50 dark:bg-violet-900/20',
            iconColor: 'text-violet-600 dark:text-violet-400',
        },
    ];
});

const measurementsData = computed(() => props.measurements?.data ?? []);
</script>

<template>
    <Head :title="title" />

    <div v-if="!isProfileComplete" class="flex h-full flex-1 flex-col p-4 md:p-6">
        <Link
            :href="`/students/${student.id}`"
            class="mb-4 flex w-fit items-center gap-2 text-sm text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
        >
            <ArrowLeft class="h-4 w-4" />
            Voltar para {{ student.name }}
        </Link>
        <CompleteProfileCard @saved="handleProfileSaved" />
    </div>

    <div v-else class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 p-6 dark:from-neutral-800 dark:via-neutral-700 dark:to-neutral-800">
            <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="`/students/${student.id}`"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20"
                    >
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold text-white">Medidas Corporais</h1>
                        <p class="text-sm text-neutral-300">{{ student.name }}</p>
                    </div>
                </div>
                <Button
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 hover:brightness-110"
                    @click="isCreateOpen = true"
                >
                    <Plus class="mr-2 h-4 w-4" />
                    Nova Medição
                </Button>
            </div>
        </div>

        <!-- Métricas Atuais -->
        <div v-if="latest" class="space-y-4">
            <h2 class="flex items-center gap-2 text-lg font-semibold text-neutral-900 dark:text-white">
                <TrendingUp class="h-5 w-5 text-emerald-500" />
                Métricas Atuais
                <span class="text-sm font-normal text-neutral-400">· {{ latest.recorded_at }}</span>
            </h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <div
                    v-for="card in metricCards"
                    :key="card.label"
                    class="group rounded-xl border border-neutral-200 bg-white p-4 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ card.label }}</p>
                            <p class="mt-1 text-2xl font-bold text-neutral-900 dark:text-white">{{ card.value }}</p>
                            <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">{{ card.sub }}</p>
                        </div>
                        <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', card.bg]">
                            <component :is="card.icon" :class="['h-5 w-5', card.iconColor]" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-neutral-300 bg-neutral-50 py-20 text-center dark:border-neutral-700 dark:bg-neutral-900/50"
        >
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-700">
                <Ruler class="h-10 w-10 text-neutral-400" />
            </div>
            <h3 class="text-xl font-bold text-neutral-900 dark:text-white">Nenhuma medição registrada</h3>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400 max-w-md">
                Registre a primeira medição corporal de {{ student.name }} para começar a acompanhar a evolução.
            </p>
            <Button
                class="mt-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 hover:brightness-110"
                @click="isCreateOpen = true"
            >
                <Plus class="mr-2 h-5 w-5" />
                Primeira Medição
            </Button>
        </div>

        <!-- Histórico -->
        <div v-if="measurementsData.length > 0" class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h3 class="font-semibold text-neutral-900 dark:text-white">Histórico de Medições</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs font-medium text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800/50 dark:text-neutral-400">
                            <th class="px-6 py-3">Data</th>
                            <th class="px-4 py-3">Peso</th>
                            <th class="px-4 py-3">Altura</th>
                            <th class="px-4 py-3">IMC</th>
                            <th class="px-4 py-3">% Gordura</th>
                            <th class="px-4 py-3">M. Magra</th>
                            <th class="px-4 py-3">TMB</th>
                            <th class="px-4 py-3">Nível</th>
                            <th class="px-4 py-3">Objetivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        <tr
                            v-for="m in measurementsData"
                            :key="m.id"
                            class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700/50"
                        >
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-neutral-900 dark:text-white">
                                {{ m.recorded_at }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-neutral-700 dark:text-neutral-300">
                                {{ m.weight }} kg
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-neutral-700 dark:text-neutral-300">
                                {{ m.height }} cm
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span
                                    v-if="m.metrics"
                                    class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium"
                                    :style="{
                                        backgroundColor: m.metrics.bmi.color + '20',
                                        color: m.metrics.bmi.color,
                                    }"
                                >
                                    {{ m.metrics.bmi.value }}
                                </span>
                                <span v-else class="text-neutral-400">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-neutral-700 dark:text-neutral-300">
                                <span v-if="m.metrics">{{ m.metrics.body_fat.value }}%</span>
                                <span v-else class="text-neutral-400">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-neutral-700 dark:text-neutral-300">
                                <span v-if="m.metrics">{{ m.metrics.lean_mass }} kg</span>
                                <span v-else class="text-neutral-400">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-neutral-700 dark:text-neutral-300">
                                <span v-if="m.metrics">{{ m.metrics.bmr }} kcal</span>
                                <span v-else class="text-neutral-400">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-xs text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                    {{ m.activity_level }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    {{ m.goal }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div
                v-if="measurements.meta?.last_page > 1"
                class="flex items-center justify-between border-t border-neutral-200 px-6 py-4 dark:border-neutral-700"
            >
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    Página {{ measurements.meta.current_page }} de {{ measurements.meta.last_page }}
                </p>
                <div class="flex gap-2">
                    <Button
                        v-if="measurements.meta.prev_page_url"
                        variant="outline"
                        size="sm"
                        @click="router.visit(measurements.meta.prev_page_url)"
                    >
                        Anterior
                    </Button>
                    <Button
                        v-if="measurements.meta.next_page_url"
                        variant="outline"
                        size="sm"
                        @click="router.visit(measurements.meta.next_page_url)"
                    >
                        Próxima
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sheet Nova Medição -->
    <Sheet v-model:open="isCreateOpen">
        <SheetContent side="right" class="w-full p-0 sm:max-w-xl">
            <SheetHeader class="sr-only">
                <SheetTitle>Nova Medição</SheetTitle>
            </SheetHeader>
            <CreateBodyMeasurementSheet
                v-if="student"
                :student="student"
                @close="isCreateOpen = false"
                @saved="isCreateOpen = false"
            />
        </SheetContent>
    </Sheet>

    <ToastContainer v-model="toasts" />
</template>
