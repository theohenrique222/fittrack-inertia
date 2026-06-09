<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Financeiro',
                href: '/financial',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    Banknote,
    Calendar,
    DollarSign,
    TrendingUp,
    Users,
    Clock,
    CheckCircle,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';

interface PaymentItem {
    id: number;
    client_id: number;
    amount: number;
    due_date: string;
    paid_at: string | null;
    payment_method: string | null;
    status: 'pending' | 'paid' | 'overdue';
    plan_name: string | null;
    client_name: string;
    client_nickname: string | null;
}

interface StudentOption {
    id: number;
    name: string;
}

interface Stats {
    month_revenue: number;
    month_pending: number;
    month_predicted_revenue: number;
    total_received: number;
    total_pending: number;
    total_overdue: number;
    overdue_students_count: number;
}

interface FilteredTotals {
    total_received: number;
    total_pending: number;
    total_overdue: number;
    payment_count: number;
}

const props = defineProps<{
    title: string;
    stats: Stats;
    filtered_totals: FilteredTotals;
    latest_payments: PaymentItem[];
    upcoming_dues: PaymentItem[];
    overdue_payments: PaymentItem[];
    students: StudentOption[];
    selected_period: string;
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const selectedPeriod = ref(props.selected_period);
const selectedStudent = ref('all');
const selectedStatus = ref('all');
const selectedPaymentMethod = ref('all');

const paymentMethods = [
    { value: 'all', label: 'Todos' },
    { value: 'pix', label: 'PIX' },
    { value: 'credit_card', label: 'Cartão de Crédito' },
    { value: 'boleto', label: 'Boleto' },
    { value: 'cash', label: 'Dinheiro' },
    { value: 'transfer', label: 'Transferência' },
];

function applyFilters() {
    const params: Record<string, string> = {};

    if (selectedPeriod.value) {
params.period = selectedPeriod.value;
}

    if (selectedStudent.value && selectedStudent.value !== 'all') {
params.student_id = selectedStudent.value;
}

    if (selectedStatus.value && selectedStatus.value !== 'all') {
params.status = selectedStatus.value;
}

    if (selectedPaymentMethod.value && selectedPaymentMethod.value !== 'all') {
params.payment_method = selectedPaymentMethod.value;
}

    router.get('/financial', params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function clearFilters() {
    selectedPeriod.value = '30d';
    selectedStudent.value = 'all';
    selectedStatus.value = 'all';
    selectedPaymentMethod.value = 'all';
    applyFilters();
}

const hasActiveFilters = computed(() =>
    selectedStudent.value !== 'all' || selectedStatus.value !== 'all' || selectedPaymentMethod.value !== 'all',
);

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

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}

function formatPaymentMethod(method: string | null): string {
    if (!method) {
return '---';
}

    const map: Record<string, string> = {
        pix: 'PIX',
        credit_card: 'Cartão',
        boleto: 'Boleto',
        cash: 'Dinheiro',
        transfer: 'Transferência',
    };

    return map[method] || method;
}

</script>

<template>
    <Head :title="title" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6">
        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-6 md:p-8 text-white shadow-xl">
            <div class="pointer-events-none absolute -inset-[100%] animate-[shimmer_3s_ease-in-out_infinite] bg-[repeating-linear-gradient(90deg,transparent,transparent_40%,rgba(255,255,255,0.06)_50%,transparent_60%,transparent_100%)] bg-[length:200%_100%]" />
            <div class="absolute top-0 right-0 h-64 w-64 rounded-full bg-white/10 blur-3xl -translate-y-32 translate-x-32" />
            <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-white/5 blur-3xl translate-y-24 -translate-x-24" />
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-2">
                    <DollarSign class="h-8 w-8" />
                    <h1 class="text-2xl md:text-3xl font-bold">Financeiro</h1>
                </div>
                <p class="text-sm md:text-base opacity-90 mt-1 max-w-xl">
                    Acompanhe receitas, mensalidades e inadimplência
                </p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            <div class="group rounded-xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-900/30 dark:bg-neutral-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Receita do Mês</p>
                        <h2 class="mt-1 text-2xl font-bold text-neutral-900 dark:text-white">{{ formatCurrency(stats.month_revenue) }}</h2>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                        <TrendingUp class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-900/30 dark:bg-neutral-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Receita Prevista</p>
                        <h2 class="mt-1 text-2xl font-bold text-neutral-900 dark:text-white">{{ formatCurrency(stats.month_predicted_revenue) }}</h2>
                        <p class="mt-0.5 text-xs text-neutral-400">{{ formatCurrency(stats.month_pending) }} pendente</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                        <Banknote class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-900/30 dark:bg-neutral-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Total Recebido</p>
                        <h2 class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(stats.total_received) }}</h2>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                        <CheckCircle class="h-5 w-5 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-900/30 dark:bg-neutral-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Total Pendente</p>
                        <h2 class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ formatCurrency(stats.total_pending) }}</h2>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/30">
                        <Clock class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                </div>
            </div>

            <div class="group rounded-xl border border-emerald-100 bg-white p-5 shadow-sm dark:border-emerald-900/30 dark:bg-neutral-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Inadimplentes</p>
                        <h2 class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.overdue_students_count }}</h2>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                        <Users class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label class="mb-1.5 block text-xs font-medium text-neutral-500 dark:text-neutral-400">Período</label>
                    <div class="flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-700">
                        <button
                            v-for="opt in [{ value: '7d', label: '7 dias' }, { value: '30d', label: '30 dias' }, { value: '90d', label: '90 dias' }, { value: '12m', label: '12 meses' }]"
                            :key="opt.value"
                            @click="selectedPeriod = opt.value; applyFilters()"
                            :class="[
                                'flex-1 rounded-md px-3 py-1.5 text-xs font-medium transition-all',
                                selectedPeriod === opt.value
                                    ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-600 dark:text-white'
                                    : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white',
                            ]"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>

                <div class="w-full lg:w-48">
                    <label class="mb-1.5 block text-xs font-medium text-neutral-500 dark:text-neutral-400">Aluno</label>
                    <Select v-model="selectedStudent" @update:model-value="applyFilters()">
                        <SelectTrigger>
                            <SelectValue placeholder="Todos os alunos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todos os alunos</SelectItem>
                            <SelectItem v-for="s in students" :key="s.id" :value="String(s.id)">
                                {{ s.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="w-full lg:w-40">
                    <label class="mb-1.5 block text-xs font-medium text-neutral-500 dark:text-neutral-400">Status</label>
                    <Select v-model="selectedStatus" @update:model-value="applyFilters()">
                        <SelectTrigger>
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Todos</SelectItem>
                            <SelectItem value="pending">Pendente</SelectItem>
                            <SelectItem value="paid">Pago</SelectItem>
                            <SelectItem value="overdue">Vencido</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="w-full lg:w-44">
                    <label class="mb-1.5 block text-xs font-medium text-neutral-500 dark:text-neutral-400">Forma de Pagamento</label>
                    <Select v-model="selectedPaymentMethod" @update:model-value="applyFilters()">
                        <SelectTrigger>
                            <SelectValue placeholder="Todos" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="pm in paymentMethods" :key="pm.value" :value="pm.value">
                                {{ pm.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    size="sm"
                    class="shrink-0 text-neutral-500"
                    @click="clearFilters"
                >
                    <X class="mr-1 h-3.5 w-3.5" />
                    Limpar
                </Button>
            </div>
        </div>

        <!-- Filtered Totals -->
        <div v-if="hasActiveFilters || selectedPeriod !== '30d'" class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <p class="mb-3 text-sm font-medium text-neutral-700 dark:text-neutral-300">
                Resultados dos Filtros
            </p>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Recebido</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ formatCurrency(filtered_totals.total_received) }}</p>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Pendente</p>
                    <p class="text-lg font-bold text-amber-600 dark:text-amber-400">{{ formatCurrency(filtered_totals.total_pending) }}</p>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Total Vencido</p>
                    <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ formatCurrency(filtered_totals.total_overdue) }}</p>
                </div>
                <div>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Quantidade</p>
                    <p class="text-lg font-bold text-neutral-900 dark:text-white">{{ filtered_totals.payment_count }}</p>
                </div>
            </div>
        </div>

        <!-- Lists -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Últimos Pagamentos -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Últimos Pagamentos</h3>
                    <Badge variant="default" class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        {{ latest_payments.length }}
                    </Badge>
                </div>
                <div class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    <div v-if="latest_payments.length === 0" class="flex flex-col items-center py-8 text-center">
                        <DollarSign class="mb-2 h-8 w-8 text-neutral-300 dark:text-neutral-600" />
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Nenhum pagamento recebido</p>
                    </div>
                    <div
                        v-for="payment in latest_payments"
                        :key="payment.id"
                        class="flex items-center justify-between px-5 py-3 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700/30"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ payment.client_name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ payment.paid_at }} · {{ formatPaymentMethod(payment.payment_method) }}</p>
                        </div>
                        <span class="ml-3 text-sm font-semibold text-green-600 dark:text-green-400 tabular-nums">
                            {{ formatCurrency(payment.amount) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Próximos Vencimentos -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Próximos Vencimentos</h3>
                    <Badge variant="secondary">{{ upcoming_dues.length }}</Badge>
                </div>
                <div class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    <div v-if="upcoming_dues.length === 0" class="flex flex-col items-center py-8 text-center">
                        <Calendar class="mb-2 h-8 w-8 text-neutral-300 dark:text-neutral-600" />
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Nenhum vencimento próximo</p>
                    </div>
                    <div
                        v-for="payment in upcoming_dues"
                        :key="payment.id"
                        class="flex items-center justify-between px-5 py-3 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700/30"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white truncate">{{ payment.client_name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Vence {{ payment.due_date }}</p>
                        </div>
                        <span class="ml-3 text-sm font-semibold text-amber-600 dark:text-amber-400 tabular-nums">
                            {{ formatCurrency(payment.amount) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Pagamentos Atrasados -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-4 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-white">Pagamentos Atrasados</h3>
                    <Badge variant="destructive">{{ overdue_payments.length }}</Badge>
                </div>
                <div class="divide-y divide-neutral-100 dark:divide-neutral-700">
                    <div v-if="overdue_payments.length === 0" class="flex flex-col items-center py-8 text-center">
                        <CheckCircle class="mb-2 h-8 w-8 text-neutral-300 dark:text-neutral-600" />
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Nenhum pagamento atrasado</p>
                    </div>
                    <div
                        v-for="payment in overdue_payments"
                        :key="payment.id"
                        class="flex items-center justify-between px-5 py-3 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-700/30"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-red-600 dark:text-red-400 truncate">{{ payment.client_name }}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Venceu {{ payment.due_date }}</p>
                        </div>
                        <span class="ml-3 text-sm font-semibold text-red-600 dark:text-red-400 tabular-nums">
                            {{ formatCurrency(payment.amount) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
