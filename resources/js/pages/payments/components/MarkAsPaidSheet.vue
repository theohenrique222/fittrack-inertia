<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { AlertCircle, Calendar, Hash } from 'lucide-vue-next';
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

interface Payment {
    id: number;
    amount: number;
}

const props = defineProps<{
    payment: Payment;
}>();

const emit = defineEmits(['close']);

const form = useForm({
    paid_at: new Date().toISOString().split('T')[0],
    payment_method: '',
});

function handleSubmit() {
    form.put(`/payments/${props.payment.id}/mark-as-paid`, {
            preserveScroll: true,
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
            <div class="rounded-lg bg-muted/50 p-4">
                <p class="text-sm text-muted-foreground">
                    Valor a receber:
                    <span class="font-semibold text-foreground">
                        {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(payment.amount) }}
                    </span>
                </p>
            </div>

            <div class="space-y-2">
                <Label for="paid_at" class="text-sm font-medium">
                    Data do Pagamento <span class="text-destructive">*</span>
                </Label>
                <div class="relative">
                    <Calendar class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="paid_at"
                        v-model="form.paid_at"
                        type="date"
                        class="pl-9"
                        :class="{ 'border-destructive ring-destructive/50': form.errors.paid_at }"
                    />
                </div>
                <p v-if="form.errors.paid_at" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.paid_at }}
                </p>
            </div>

            <div class="space-y-2">
                <Label for="payment_method" class="text-sm font-medium">
                    Forma de Pagamento
                </Label>
                <Select v-model="form.payment_method">
                    <SelectTrigger :class="{ 'border-destructive ring-destructive/50': form.errors.payment_method }">
                        <SelectValue placeholder="Selecione a forma de pagamento" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="pix">PIX</SelectItem>
                        <SelectItem value="credit_card">Cartão de Crédito</SelectItem>
                        <SelectItem value="boleto">Boleto</SelectItem>
                        <SelectItem value="cash">Dinheiro</SelectItem>
                        <SelectItem value="transfer">Transferência</SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="form.errors.payment_method" class="flex items-center gap-1 text-xs text-destructive">
                    <AlertCircle class="h-3 w-3" />
                    {{ form.errors.payment_method }}
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
                    Confirmar Pagamento
                </template>
            </Button>
        </div>
    </form>
</template>
