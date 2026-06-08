<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Planos',
                href: '/plans',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    CreditCard,
    Users,
    Calendar,
    Pencil,
    Plus,
    Search,
    Trash2,
    Check,
    X as XIcon,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
import CreatePlanSheet from '@/pages/plans/components/CreatePlanSheet.vue';
import EditPlanSheet from '@/pages/plans/components/EditPlanSheet.vue';
import { destroy } from '@/routes/plans';

interface Plan {
    id: number;
    name: string;
    description?: string;
    price: number;
    duration_months: number;
    is_active: boolean;
    clients_count: number;
}

const props = defineProps<{
    title: string;
    plans: Plan[];
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const searchQuery = ref('');

const filteredPlans = computed(() => {
    let result = props.plans;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (p) =>
                p.name.toLowerCase().includes(query) ||
                p.description?.toLowerCase().includes(query),
        );
    }

    return result;
});

const stats = computed(() => {
    const plans = props.plans || [];

    return {
        total: plans.length,
        active: plans.filter((p) => p.is_active).length,
        totalStudents: plans.reduce((acc, p) => acc + (p.clients_count || 0), 0),
    };
});

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
    { immediate: true },
);

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const selectedPlan = ref<Plan | null>(null);

const handleEditClick = (plan: Plan) => {
    selectedPlan.value = plan;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este plano?')) {
        return;
    }

    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedPlan.value = null;
        },
    });
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

function formatPrice(value: number): string {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}
</script>

<template>
    <Head title="Planos" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white"
        >
            <div
                class="pointer-events-none absolute -inset-[100%] animate-[shimmer_3s_ease-in-out_infinite] bg-[repeating-linear-gradient(90deg,transparent,transparent_40%,rgba(255,255,255,0.06)_50%,transparent_60%,transparent_100%)] bg-[length:200%_100%]"
            />

            <div class="relative z-10 mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Planos</h1>
                    <p class="mt-1 text-sm text-emerald-100/80">
                        Gerencie os planos de assinatura do sistema
                    </p>
                </div>

                <Sheet v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button
                            class="inline-flex items-center gap-2 border-0 bg-white text-emerald-700 shadow-lg transition-all hover:bg-emerald-50 hover:shadow-xl active:scale-95"
                        >
                            <Plus class="h-4 w-4" />
                            Novo Plano
                        </Button>
                    </SheetTrigger>

                    <SheetContent side="right" class="flex flex-col p-0 sm:max-w-md">
                        <SheetHeader class="border-b border-border px-4 py-4">
                            <SheetTitle>Criar Plano</SheetTitle>
                            <SheetDescription>
                                Preencha os dados para cadastrar um novo plano de assinatura
                            </SheetDescription>
                        </SheetHeader>

                        <CreatePlanSheet @close="closeCreateSheet" />
                    </SheetContent>
                </Sheet>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-3">
                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <CreditCard class="h-4 w-4 text-emerald-100/70" />
                        <span class="text-xs font-medium text-emerald-100/70">Total</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.total }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Check class="h-4 w-4 text-emerald-100/70" />
                        <span class="text-xs font-medium text-emerald-100/70">Ativos</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.active }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Users class="h-4 w-4 text-emerald-100/70" />
                        <span class="text-xs font-medium text-emerald-100/70">Alunos</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.totalStudents }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 px-6 py-5 dark:bg-neutral-900">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-neutral-400"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Buscar plano por nome..."
                        class="border-neutral-200 bg-white pl-9 transition-all focus:border-emerald-400 focus:ring-emerald-400 dark:border-neutral-700 dark:bg-neutral-800"
                    />
                    <button
                        v-if="searchQuery"
                        class="absolute top-1/2 right-3 -translate-y-1/2 text-neutral-400 transition-colors hover:text-neutral-600"
                        @click="searchQuery = ''"
                    >
                        <XIcon class="h-4 w-4" />
                    </button>
                </div>
            </div>

            <div
                v-if="!filteredPlans.length"
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <div
                    class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/40 dark:to-emerald-800/40"
                >
                    <CreditCard class="h-10 w-10 text-emerald-400 dark:text-emerald-500" />
                </div>
                <p class="text-lg font-semibold text-neutral-700 dark:text-neutral-300">
                    Nenhum plano cadastrado
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    Clique em "Novo Plano" para começar
                </p>
            </div>

            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="plan in filteredPlans"
                    :key="plan.id"
                    class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800"
                >
                    <div
                        class="absolute top-0 left-0 h-1 w-full bg-gradient-to-r from-emerald-500 to-teal-500"
                    />

                    <div class="p-5">
                        <div class="mb-4 flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">
                                    {{ plan.name }}
                                </h3>
                                <p
                                    v-if="plan.description"
                                    class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 line-clamp-2"
                                >
                                    {{ plan.description }}
                                </p>
                            </div>
                            <Badge
                                :variant="plan.is_active ? 'default' : 'secondary'"
                            >
                                {{ plan.is_active ? 'Ativo' : 'Inativo' }}
                            </Badge>
                        </div>

                        <div class="mb-4">
                            <span class="text-3xl font-bold text-neutral-900 dark:text-white">
                                {{ formatPrice(plan.price) }}
                            </span>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400">/mês</span>
                        </div>

                        <div class="mb-5 space-y-2">
                            <div class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <Calendar class="h-4 w-4" />
                                <span>{{ plan.duration_months }} {{ plan.duration_months === 1 ? 'mês' : 'meses' }} de duração</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                <Users class="h-4 w-4" />
                                <span>{{ plan.clients_count || 0 }} {{ plan.clients_count === 1 ? 'aluno' : 'alunos' }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg border border-neutral-200 bg-white px-3 py-2 text-sm font-medium text-neutral-700 shadow-sm transition-all hover:border-emerald-300 hover:text-emerald-700 hover:shadow-md active:scale-[0.98] dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:border-emerald-600 dark:hover:text-emerald-300"
                                @click="handleEditClick(plan)"
                            >
                                <Pencil class="h-4 w-4" />
                                Editar
                            </button>
                            <button
                                class="flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 shadow-sm transition-all hover:border-red-300 hover:bg-red-50 hover:shadow-md active:scale-[0.98] dark:border-red-800 dark:bg-neutral-800 dark:text-red-400 dark:hover:border-red-600 dark:hover:bg-red-950/30"
                                @click="handleDeleteClick(plan.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Dialog v-model:open="isEditOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Plano</DialogTitle>
            </DialogHeader>

            <EditPlanSheet
                v-if="selectedPlan"
                :plan="selectedPlan"
                @close="closeEditSheet"
            />
        </DialogContent>
    </Dialog>
</template>

<style scoped>
@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
</style>
