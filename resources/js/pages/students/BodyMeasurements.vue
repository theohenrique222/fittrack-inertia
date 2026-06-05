<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Activity,
    ArrowLeft,
    CalendarDays,
    Crosshair,
    Droplets,
    Dumbbell,
    Flame,
    Pencil,
    Plus,
    Ruler,
    Scale,
    Trash2,
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
import EditBodyMeasurementSheet from '@/pages/students/components/EditBodyMeasurementSheet.vue';

const page = usePage();
const { toasts, success, error } = useToast();

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
success(flash.success);
}

        if (flash?.measurement_warnings) {
            const warnings = Array.isArray(flash.measurement_warnings)
                ? flash.measurement_warnings
                : [flash.measurement_warnings];

            warnings.forEach((warning: string) => {
                success(warning);
            });
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
const isEditOpen = ref(false);
const selectedMeasurement = ref<Measurement | null>(null);
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

function handleDeleteMeasurement(id: number) {
    if (!confirm('Tem certeza que deseja excluir esta medida?')) {
        return;
    }

    router.delete(`/students/${props.student.id}/measurements/${id}`);
}

function handleEditClick(measurement: Measurement) {
    selectedMeasurement.value = measurement;
    isEditOpen.value = true;
}

function getBadgeClass(color: string): string {
    const colorMap: Record<string, string> = {
        '#059669': 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        '#d97706': 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        '#dc2626': 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        '#2563eb': 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        '#7c3aed': 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-400',
    };

    return colorMap[color] ?? 'bg-neutral-50 text-neutral-700 dark:bg-neutral-900/30 dark:text-neutral-400';
}

function formatDateDay(date: string): string {
    const d = new Date(date);

    return d.getDate().toString().padStart(2, '0');
}

function formatDateFull(date: string): string {
    const d = new Date(date);

    return d.toLocaleDateString('pt-BR', { day: 'numeric', month: 'long', year: 'numeric' });
}

function formatTime(date: string): string {
    const d = new Date(date);

    if (d.getHours() === 0 && d.getMinutes() === 0) {
return '';
}

    return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

const activityLabels: Record<string, string> = {
    sedentary: 'Sedentário',
    light: 'Leve',
    moderate: 'Moderado',
    active: 'Ativo',
    very_active: 'Intenso',
};

const goalLabels: Record<string, string> = {
    maintain: 'Manter',
    lose: 'Perder gordura',
    gain: 'Ganhar massa',
};

function activityLabel(value: string): string {
    return activityLabels[value] ?? value;
}

function goalLabel(value: string): string {
    return goalLabels[value] ?? value;
}
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

    <div v-else class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 md:p-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 p-6 shadow-xl dark:from-neutral-900 dark:via-neutral-800 dark:to-neutral-900 md:p-8">
            <div class="absolute -top-20 -right-20 h-80 w-80 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/3 h-40 w-40 rounded-full bg-teal-500/5 blur-2xl"></div>

            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="`/students/${student.id}`"
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white backdrop-blur-sm transition-all hover:bg-white/20 hover:shadow-lg"
                    >
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white">Medidas Corporais</h1>
                        <p class="mt-0.5 text-sm text-neutral-400">{{ student.name }}</p>
                    </div>
                </div>
                <Button
                    class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:scale-105 hover:brightness-110"
                    @click="isCreateOpen = true"
                >
                    <Plus class="mr-2 h-4 w-4" />
                    Nova Medição
                </Button>
            </div>
        </div>

        <!-- Métricas Atuais -->
        <div v-if="latest" class="space-y-5">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600">
                    <TrendingUp class="h-4 w-4 text-white" />
                </div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Métricas Atuais
                </h2>
                <span class="text-sm text-neutral-400">· {{ latest.recorded_at }}</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <div
                    v-for="card in metricCards"
                    :key="card.label"
                    class="group relative overflow-hidden rounded-xl border border-neutral-200/60 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg dark:border-neutral-700/60 dark:bg-neutral-800/80"
                >
                    <div class="absolute top-0 right-0 h-24 w-24 rounded-bl-full opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                        :class="card.bg">
                    </div>
                    <div class="relative flex items-start gap-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl shadow-sm"
                            :class="[card.bg, card.iconColor]">
                            <component :is="card.icon" class="h-6 w-6" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium tracking-wide text-neutral-500 dark:text-neutral-400 uppercase">{{ card.label }}</p>
                            <p class="mt-1 truncate text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">{{ card.value }}</p>
                            <p class="mt-0.5 truncate text-xs font-medium text-neutral-500 dark:text-neutral-400">{{ card.sub }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-neutral-300 bg-gradient-to-b from-neutral-50 to-white py-24 text-center dark:border-neutral-700 dark:from-neutral-900/30 dark:to-neutral-900/10"
        >
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-neutral-100 to-neutral-200 shadow-inner dark:from-neutral-800 dark:to-neutral-700">
                <Ruler class="h-12 w-12 text-neutral-400" />
            </div>
            <h3 class="text-2xl font-bold text-neutral-900 dark:text-white">Nenhuma medição registrada</h3>
            <p class="mt-2 max-w-md text-sm text-neutral-500 dark:text-neutral-400">
                Registre a primeira medição corporal de {{ student.name }} para começar a acompanhar a evolução.
            </p>
            <Button
                class="mt-8 bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:scale-105 hover:brightness-110"
                @click="isCreateOpen = true"
            >
                <Plus class="mr-2 h-5 w-5" />
                Primeira Medição
            </Button>
        </div>

        <!-- Histórico -->
        <div v-if="measurementsData.length > 0" class="space-y-5">
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-neutral-600 to-neutral-700">
                    <CalendarDays class="h-4 w-4 text-white" />
                </div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Histórico de Medições
                </h2>
                <span class="rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    {{ measurementsData.length }}
                </span>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(m, index) in measurementsData"
                    :key="m.id"
                    class="group relative"
                >
                    <!-- Timeline line -->
                    <div
                        v-if="index < measurementsData.length - 1"
                        class="absolute left-6 top-14 bottom-0 w-px bg-gradient-to-b from-emerald-200 via-emerald-100 to-transparent dark:from-emerald-800 dark:via-emerald-900/50"
                    ></div>

                    <div class="relative flex items-start gap-5">
                        <!-- Timeline dot -->
                        <div class="relative z-10 flex shrink-0 items-center justify-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl border-2 border-emerald-200 bg-white shadow-sm dark:border-emerald-700 dark:bg-neutral-800">
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ formatDateDay(m.recorded_at) }}
                                </span>
                            </div>
                        </div>

                        <!-- Card -->
                        <div class="min-w-0 flex-1 rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800/80 dark:hover:border-emerald-700">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">
                                        {{ formatDateFull(m.recorded_at) }}
                                    </p>
                                    <p class="mt-0.5 text-sm text-neutral-400 dark:text-neutral-500">
                                        {{ formatTime(m.recorded_at) }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 opacity-0 transition-all hover:bg-emerald-50 hover:text-emerald-600 group-hover:opacity-100 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400"
                                        title="Editar medida"
                                        @click="handleEditClick(m)"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </button>
                                    <button
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 opacity-0 transition-all hover:bg-red-50 hover:text-red-600 group-hover:opacity-100 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                                        title="Excluir medida"
                                        @click="handleDeleteMeasurement(m.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Key metrics grid -->
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div class="rounded-lg bg-neutral-50 p-3 dark:bg-neutral-700/50">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Peso</p>
                                    <p class="mt-0.5 text-lg font-bold text-neutral-900 dark:text-white">{{ m.weight }} <span class="text-sm font-medium text-neutral-400">kg</span></p>
                                </div>
                                <div class="rounded-lg bg-neutral-50 p-3 dark:bg-neutral-700/50">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Altura</p>
                                    <p class="mt-0.5 text-lg font-bold text-neutral-900 dark:text-white">{{ m.height }} <span class="text-sm font-medium text-neutral-400">cm</span></p>
                                </div>
                                <div class="rounded-lg bg-neutral-50 p-3 dark:bg-neutral-700/50">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">IMC</p>
                                    <p v-if="m.metrics" class="mt-0.5 text-lg font-bold text-neutral-900 dark:text-white">
                                        {{ m.metrics.bmi.value }}
                                        <span class="ml-1 inline-block rounded-full px-2 py-0.5 text-xs font-semibold align-middle" :class="getBadgeClass(m.metrics.bmi.color)">
                                            {{ m.metrics.bmi.classification }}
                                        </span>
                                    </p>
                                    <p v-else class="mt-0.5 text-neutral-400">—</p>
                                </div>
                                <div class="rounded-lg bg-neutral-50 p-3 dark:bg-neutral-700/50">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">% Gordura</p>
                                    <p v-if="m.metrics" class="mt-0.5 text-lg font-bold text-neutral-900 dark:text-white">
                                        {{ m.metrics.body_fat.value }}<span class="text-sm font-medium text-neutral-400">%</span>
                                    </p>
                                    <p v-else class="mt-0.5 text-neutral-400">—</p>
                                </div>
                            </div>

                            <!-- Secondary info row -->
                            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                                <span v-if="m.metrics" class="inline-flex items-center gap-1">
                                    <Flame class="h-3.5 w-3.5 text-orange-400" />
                                    TMB: {{ m.metrics.bmr }} kcal
                                </span>
                                <span v-if="m.metrics" class="inline-flex items-center gap-1">
                                    <Dumbbell class="h-3.5 w-3.5 text-emerald-400" />
                                    M. Magra: {{ m.metrics.lean_mass }} kg
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <Activity class="h-3.5 w-3.5 text-blue-400" />
                                    {{ activityLabel(m.activity_level) }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <Crosshair class="h-3.5 w-3.5 text-violet-400" />
                                    {{ goalLabel(m.goal) }}
                                </span>
                            </div>

                            <!-- Notes -->
                            <p v-if="m.notes" class="mt-3 border-t border-neutral-100 pt-3 text-sm italic text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                                {{ m.notes }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginação -->
            <div
                v-if="measurements.meta?.last_page > 1"
                class="flex items-center justify-between rounded-xl border border-neutral-200 bg-white px-6 py-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
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

    <!-- Sheet Editar Medição -->
    <Sheet v-model:open="isEditOpen">
        <SheetContent side="right" class="w-full p-0 sm:max-w-xl">
            <SheetHeader class="sr-only">
                <SheetTitle>Editar Medição</SheetTitle>
            </SheetHeader>
            <EditBodyMeasurementSheet
                v-if="student && selectedMeasurement"
                :student="student"
                :measurement="selectedMeasurement"
                @close="isEditOpen = false"
            />
        </SheetContent>
    </Sheet>

    <ToastContainer v-model="toasts" />
</template>
