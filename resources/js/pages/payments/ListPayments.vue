<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Mensalidades',
                href: '/payments',
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
    Check,
    ChevronDown,
    Clock,
    DollarSign,
    Plus,
    Search,
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
import CreatePaymentSheet from '@/pages/payments/components/CreatePaymentSheet.vue';
import MarkAsPaidSheet from '@/pages/payments/components/MarkAsPaidSheet.vue';

interface ClientUser {
    id: number;
    name: string;
}

interface Client {
    id: number;
    nickname: string | null;
    user: ClientUser | null;
}

interface Plan {
    id: number;
    name: string;
}

interface Payment {
    id: number;
    client_id: number;
    plan_id: number;
    amount: number;
    due_date: string;
    paid_at: string | null;
    payment_method: string | null;
    status: 'pending' | 'paid' | 'overdue';
    notes: string | null;
    client: Client | null;
    plan: Plan | null;
}

const props = defineProps<{
    title: string;
    payments: Payment[];
    filter: string | null;
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const searchQuery = ref('');
const statusFilter = ref(props.filter || '');

const filteredPayments = computed(() => {
    let result = props.payments;

    if (statusFilter.value) {
        result = result.filter((p) => p.status === statusFilter.value);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (p) =>
                p.client?.user?.name?.toLowerCase().includes(query) ||
                p.client?.nickname?.toLowerCase().includes(query) ||
                p.plan?.name?.toLowerCase().includes(query),
        );
    }

    return result;
});

const stats = computed(() => {
    const payments = props.payments || [];

    return {
        total: payments.length,
        totalAmount: payments.reduce((acc, p) => acc + p.amount, 0),
        pending: payments.filter((p) => p.status === 'pending').length,
        overdue: payments.filter((p) => p.status === 'overdue').length,
        paid: payments.filter((p) => p.status === 'paid').length,
    };
});

function applyFilter(filter: string | null) {
    statusFilter.value = filter || '';

    const url = filter ? `/payments?filter=${filter}` : '/payments';
    router.get(url, {}, { preserveState: true, preserveScroll: true });
}

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
const isMarkAsPaidOpen = ref(false);
const selectedPayment = ref<Payment | null>(null);

const handleMarkAsPaidClick = (payment: Payment) => {
    selectedPayment.value = payment;
    isMarkAsPaidOpen.value = true;
};

const handleReopenClick = (payment: Payment) => {
    if (!confirm('Tem certeza que deseja reabrir este pagamento?')) {
        return;
    }

    router.put(`/payments/${payment.id}/reopen`, {}, {
        preserveScroll: true,
    });
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
};

const closeMarkAsPaidSheet = () => {
    isMarkAsPaidOpen.value = false;
};

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value);
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
}

function statusVariant(status: string) {
    switch (status) {
        case 'paid':
            return 'default';
        case 'overdue':
            return 'destructive';
        default:
            return 'secondary';
    }
}
</script>

<template>
    <Head title="Mensalidades" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-violet-700 to-violet-800 px-6 py-8 text-white"
        >
            <div
                class="pointer-events-none absolute -inset-[100%] animate-[shimmer_3s_ease-in-out_infinite] bg-[repeating-linear-gradient(90deg,transparent,transparent_40%,rgba(255,255,255,0.06)_50%,transparent_60%,transparent_100%)] bg-[length:200%_100%]"
            />

            <div class="relative z-10 mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Mensalidades</h1>
                    <p class="mt-1 text-sm text-violet-100/80">
                        Gerencie os pagamentos dos alunos
                    </p>
                </div>

                <Sheet v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button
                            class="inline-flex items-center gap-2 border-0 bg-white text-violet-700 shadow-lg transition-all hover:bg-violet-50 hover:shadow-xl active:scale-95"
                        >
                            <Plus class="h-4 w-4" />
                            Novo Pagamento
                        </Button>
                    </SheetTrigger>

                    <SheetContent side="right" class="flex flex-col p-0 sm:max-w-md">
                        <SheetHeader class="border-b border-border px-4 py-4">
                            <SheetTitle>Registrar Pagamento</SheetTitle>
                            <SheetDescription>
                                Preencha os dados para registrar um novo pagamento
                            </SheetDescription>
                        </SheetHeader>

                        <CreatePaymentSheet @close="closeCreateSheet" />
                    </SheetContent>
                </Sheet>
            </div>

            <div class="relative z-10 grid grid-cols-4 gap-3">
                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <DollarSign class="h-4 w-4 text-violet-100/70" />
                        <span class="text-xs font-medium text-violet-100/70">Total</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ formatCurrency(stats.totalAmount) }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Clock class="h-4 w-4 text-violet-100/70" />
                        <span class="text-xs font-medium text-violet-100/70">Pendentes</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.pending }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <Banknote class="h-4 w-4 text-violet-100/70" />
                        <span class="text-xs font-medium text-violet-100/70">Pagos</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.paid }}
                    </p>
                </div>

                <div
                    class="group rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm transition-all hover:bg-white/20"
                >
                    <div class="mb-1 flex items-center gap-2">
                        <ChevronDown class="h-4 w-4 text-violet-100/70" />
                        <span class="text-xs font-medium text-violet-100/70">Vencidos</span>
                    </div>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">
                        {{ stats.overdue }}
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
                        placeholder="Buscar por aluno ou plano..."
                        class="border-neutral-200 bg-white pl-9 transition-all focus:border-violet-400 focus:ring-violet-400 dark:border-neutral-700 dark:bg-neutral-800"
                    />
                    <button
                        v-if="searchQuery"
                        class="absolute top-1/2 right-3 -translate-y-1/2 text-neutral-400 transition-colors hover:text-neutral-600"
                        @click="searchQuery = ''"
                    >
                        <XIcon class="h-4 w-4" />
                    </button>
                </div>

                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :class="{ 'bg-violet-100 text-violet-700 border-violet-300': !statusFilter }"
                        @click="applyFilter(null)"
                    >
                        Todos
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :class="{ 'bg-amber-100 text-amber-700 border-amber-300': statusFilter === 'pending' }"
                        @click="applyFilter('pending')"
                    >
                        Pendentes
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :class="{ 'bg-green-100 text-green-700 border-green-300': statusFilter === 'paid' }"
                        @click="applyFilter('paid')"
                    >
                        Pago
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :class="{ 'bg-red-100 text-red-700 border-red-300': statusFilter === 'overdue' }"
                        @click="applyFilter('overdue')"
                    >
                        Vencidos
                    </Button>
                </div>
            </div>

            <div
                v-if="!filteredPayments.length"
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <div
                    class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-violet-100 to-violet-200 dark:from-violet-900/40 dark:to-violet-800/40"
                >
                    <DollarSign class="h-10 w-10 text-violet-400 dark:text-violet-500" />
                </div>
                <p class="text-lg font-semibold text-neutral-700 dark:text-neutral-300">
                    Nenhum pagamento encontrado
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    Clique em "Novo Pagamento" para registrar
                </p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-neutral-200 text-left text-sm font-medium text-neutral-500 dark:border-neutral-700 dark:text-neutral-400">
                            <th class="pb-3 pr-4">Aluno</th>
                            <th class="pb-3 pr-4">Plano</th>
                            <th class="pb-3 pr-4">Valor</th>
                            <th class="pb-3 pr-4">Vencimento</th>
                            <th class="pb-3 pr-4">Status</th>
                            <th class="pb-3 pr-4">Pagamento</th>
                            <th class="pb-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="payment in filteredPayments"
                            :key="payment.id"
                            class="border-b border-neutral-100 transition-colors hover:bg-neutral-100/50 dark:border-neutral-800 dark:hover:bg-neutral-800/50"
                        >
                            <td class="py-3 pr-4">
                                <span class="font-medium text-neutral-900 dark:text-white">
                                    {{ payment.client?.user?.name || payment.client?.nickname || '---' }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ payment.plan?.name || '---' }}
                            </td>
                            <td class="py-3 pr-4 font-medium tabular-nums">
                                {{ formatCurrency(payment.amount) }}
                            </td>
                            <td class="py-3 pr-4 text-sm text-neutral-600 dark:text-neutral-400">
                                <div class="flex items-center gap-1.5">
                                    <Calendar class="h-3.5 w-3.5" />
                                    {{ formatDate(payment.due_date) }}
                                </div>
                            </td>
                            <td class="py-3 pr-4">
                                <Badge :variant="statusVariant(payment.status)">
                                    {{ payment.status === 'paid' ? 'Pago' : payment.status === 'overdue' ? 'Vencido' : 'Pendente' }}
                                </Badge>
                            </td>
                            <td class="py-3 pr-4 text-sm text-neutral-600 dark:text-neutral-400">
                                <template v-if="payment.paid_at">
                                    {{ formatDate(payment.paid_at) }}
                                    <span v-if="payment.payment_method" class="ml-1 text-xs text-neutral-400">
                                        ({{ payment.payment_method === 'credit_card' ? 'Cartão' : payment.payment_method === 'boleto' ? 'Boleto' : payment.payment_method === 'pix' ? 'PIX' : payment.payment_method }})
                                    </span>
                                </template>
                                <span v-else class="text-neutral-400">---</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <Button
                                        v-if="payment.status !== 'paid'"
                                        variant="outline"
                                        size="sm"
                                        class="h-8 gap-1 text-xs"
                                        @click="handleMarkAsPaidClick(payment)"
                                    >
                                        <Check class="h-3.5 w-3.5" />
                                        Pago
                                    </Button>
                                    <Button
                                        v-if="payment.status === 'paid'"
                                        variant="outline"
                                        size="sm"
                                        class="h-8 gap-1 text-xs"
                                        @click="handleReopenClick(payment)"
                                    >
                                        Reabrir
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <Sheet v-model:open="isMarkAsPaidOpen">
        <SheetContent side="right" class="flex flex-col p-0 sm:max-w-md">
            <SheetHeader class="border-b border-border px-4 py-4">
                <SheetTitle>Marcar como Pago</SheetTitle>
                <SheetDescription>
                    Confirme os dados para marcar este pagamento como recebido
                </SheetDescription>
            </SheetHeader>

            <MarkAsPaidSheet
                v-if="selectedPayment"
                :payment="selectedPayment"
                @close="closeMarkAsPaidSheet"
            />
        </SheetContent>
    </Sheet>
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
