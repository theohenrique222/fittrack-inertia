<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, Calendar, DollarSign, Hash, User, CreditCard } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { store } from '@/routes/payments';

interface StudentOption {
    id: number;
    nickname: string | null;
    user: {
        id: number;
        name: string;
    } | null;
}

interface PlanOption {
    id: number;
    name: string;
    price: number;
}

const emit = defineEmits(['close']);

const page = usePage();

const students = computed<StudentOption[]>(() => (page.props as any).students || []);
const plans = computed<PlanOption[]>(() => (page.props as any).plans || []);

const form = useForm({
    client_id: '',
    plan_id: '',
    amount: '',
    due_date: '',
    status: 'pending',
    notes: '',
});

const selectedPlan = computed(() =>
    plans.value.find((p) => String(p.id) === form.plan_id),
);

function onPlanChange(value: string) {
    form.plan_id = value;

    if (selectedPlan.value) {
        form.amount = String(selectedPlan.value.price);
    }
}

function handleSubmit() {
    form.post(store.url(), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}

function handleCancel() {
    form.reset();
    emit('close');
}
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex flex-1 flex-col">
        <div class="flex-1 space-y-5 overflow-y-auto px-4 pb-4">
            <div class="space-y-2">
                <Label for="client_id" class="text-sm font-medium">
                    Aluno <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <User class="pointer-events-none absolute top-1/2 left-3 z-10 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Select v-model="form.client_id">
                        <SelectTrigger class="pl-9" :class="{ 'border-destructive ring-destructive/50': form.errors.client_id }">
                            <SelectValue placeholder="Selecione um aluno" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="student in students"
                                :key="student.id"
                                :value="String(student.id)"
                            >
                                {{ student.user?.name || student.nickname }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <p v-if="form.errors.client_id" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.client_id }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="plan_id" class="text-sm font-medium">
                    Plano <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <CreditCard class="pointer-events-none absolute top-1/2 left-3 z-10 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Select v-model="form.plan_id" @update:model-value="onPlanChange">
                        <SelectTrigger class="pl-9" :class="{ 'border-destructive ring-destructive/50': form.errors.plan_id }">
                            <SelectValue placeholder="Selecione um plano" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="plan in plans"
                                :key="plan.id"
                                :value="String(plan.id)"
                            >
                                {{ plan.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <p v-if="form.errors.plan_id" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.plan_id }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="amount" class="text-sm font-medium">
                    Valor <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <DollarSign class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="amount"
                        v-model="form.amount"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0,00"
                        class="pl-9"
                        :class="{ 'border-destructive ring-destructive/50': form.errors.amount }"
                    />
                </div>
                <p v-if="form.errors.amount" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.amount }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="due_date" class="text-sm font-medium">
                    Data de Vencimento <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <Calendar class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="due_date"
                        v-model="form.due_date"
                        type="date"
                        class="pl-9"
                        :class="{ 'border-destructive ring-destructive/50': form.errors.due_date }"
                    />
                </div>
                <p v-if="form.errors.due_date" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.due_date }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="notes" class="text-sm font-medium">
                    Observações
                </Label>
                <Textarea
                    id="notes"
                    v-model="form.notes"
                    placeholder="Observações sobre o pagamento..."
                    class="min-h-[80px] resize-none"
                    :class="{ 'border-destructive ring-destructive/50': form.errors.notes }"
                />
                <p v-if="form.errors.notes" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.notes }}
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-border p-4">
            <Button
                type="button"
                variant="outline"
                @click="handleCancel"
                :disabled="form.processing"
            >
                Cancelar
            </Button>

            <Button
                type="submit"
                :disabled="form.processing"
            >
                <template v-if="form.processing">
                    <Hash class="h-4 w-4 animate-spin" />
                    Salvando...
                </template>
                <template v-else>
                    Registrar Pagamento
                </template>
            </Button>
        </div>
    </form>
</template>
