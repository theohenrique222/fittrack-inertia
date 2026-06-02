<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    Activity,
    Baby,
    ChevronDown,
    Dumbbell,
    Goal,
    Ruler,
    Scale,
    Timer,
    Weight,
} from 'lucide-vue-next';
import { ref } from 'vue';
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

const props = defineProps<{
    student: { id: number; name: string };
}>();

const emit = defineEmits(['close', 'saved']);

const showCircunferences = ref(false);

const form = useForm({
    recorded_at: new Date().toISOString().split('T')[0],
    weight: '',
    height: '',
    neck: '',
    waist: '',
    hip: '',
    chest: '',
    thigh: '',
    arm: '',
    forearm: '',
    calf: '',
    shoulders: '',
    activity_level: 'moderate',
    goal: 'maintain',
    notes: '',
});

function handleSubmit() {
    form.post(`/students/${props.student.id}/measurements`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('saved');
        },
    });
}

function handleCancel() {
    form.reset();
    emit('close');
}

const activityLevels = [
    { value: 'sedentary', label: 'Sedentário', desc: 'Pouco ou nenhum exercício' },
    { value: 'light', label: 'Levemente ativo', desc: '1-3 dias/semana' },
    { value: 'moderate', label: 'Moderadamente ativo', desc: '3-5 dias/semana' },
    { value: 'active', label: 'Muito ativo', desc: '6-7 dias/semana' },
    { value: 'very_active', label: 'Extremamente ativo', desc: 'Atleta / trabalho físico' },
];

const goals = [
    { value: 'maintain', label: 'Manter peso', icon: '⚖️' },
    { value: 'lose', label: 'Perder gordura', icon: '🔥' },
    { value: 'gain', label: 'Ganhar massa', icon: '💪' },
];
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex h-full flex-col">
        <div class="flex-1 space-y-6 overflow-y-auto px-6 py-6">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Nova Medição Corporal
                </h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-0.5">
                    {{ student.name }}
                </p>
            </div>

            <!-- Dados Principais -->
            <div class="rounded-xl border border-neutral-200 bg-gradient-to-br from-emerald-50/50 to-teal-50/50 p-5 dark:border-emerald-900/30 dark:from-emerald-900/10 dark:to-teal-900/10">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                    <Scale class="h-4 w-4" />
                    Dados Principais
                </h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300">
                            <Weight class="h-3.5 w-3.5 text-emerald-500" />
                            Peso (kg) *
                        </Label>
                        <Input
                            v-model="form.weight"
                            type="number"
                            step="0.01"
                            placeholder="Ex: 73.50"
                            class="border-emerald-200 focus:border-emerald-500 dark:border-emerald-800 dark:bg-neutral-800"
                        />
                        <span v-if="form.errors.weight" class="text-xs text-red-500">{{ form.errors.weight }}</span>
                    </div>
                    <div class="space-y-1.5">
                        <Label class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300">
                            <Ruler class="h-3.5 w-3.5 text-emerald-500" />
                            Altura (cm) *
                        </Label>
                        <Input
                            v-model="form.height"
                            type="number"
                            step="0.01"
                            placeholder="Ex: 175.00"
                            class="border-emerald-200 focus:border-emerald-500 dark:border-emerald-800 dark:bg-neutral-800"
                        />
                        <span v-if="form.errors.height" class="text-xs text-red-500">{{ form.errors.height }}</span>
                    </div>
                </div>
            </div>

            <!-- Perímetro Corporal -->
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800/50">
                <button
                    type="button"
                    @click="showCircunferences = !showCircunferences"
                    class="flex w-full items-center justify-between"
                >
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                        <Ruler class="h-4 w-4 text-blue-500" />
                        Perímetro Corporal
                        <span class="ml-1 text-xs font-normal text-neutral-400">(opcional)</span>
                    </h3>
                    <ChevronDown
                        class="h-4 w-4 text-neutral-400 transition-transform duration-200"
                        :class="showCircunferences ? 'rotate-180' : ''"
                    />
                </button>
                <div
                    v-show="showCircunferences"
                    class="mt-4 grid gap-3 sm:grid-cols-3"
                >
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Pescoço (cm)</Label>
                        <Input v-model="form.neck" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Cintura (cm)</Label>
                        <Input v-model="form.waist" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Quadril (cm)</Label>
                        <Input v-model="form.hip" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Peito (cm)</Label>
                        <Input v-model="form.chest" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Coxa (cm)</Label>
                        <Input v-model="form.thigh" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Braço (cm)</Label>
                        <Input v-model="form.arm" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Antebraço (cm)</Label>
                        <Input v-model="form.forearm" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Panturrilha (cm)</Label>
                        <Input v-model="form.calf" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                    <div class="space-y-1">
                        <Label class="text-xs text-neutral-500 dark:text-neutral-400">Ombros (cm)</Label>
                        <Input v-model="form.shoulders" type="number" step="0.1" placeholder="cm" class="dark:bg-neutral-800" />
                    </div>
                </div>
            </div>

            <!-- Configurações -->
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800/50">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                    <Dumbbell class="h-4 w-4 text-orange-500" />
                    Configurações
                </h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300">
                            <Activity class="h-3.5 w-3.5 text-orange-500" />
                            Nível de Atividade *
                        </Label>
                        <Select v-model="form.activity_level">
                            <SelectTrigger class="w-full dark:bg-neutral-800">
                                <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="level in activityLevels"
                                    :key="level.value"
                                    :value="level.value"
                                >
                                    <div class="flex flex-col">
                                        <span>{{ level.label }}</span>
                                        <span class="text-xs text-neutral-400">{{ level.desc }}</span>
                                    </div>
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.activity_level" class="text-xs text-red-500">{{ form.errors.activity_level }}</span>
                    </div>

                    <div class="space-y-1.5">
                        <Label class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300">
                            <Goal class="h-3.5 w-3.5 text-orange-500" />
                            Objetivo *
                        </Label>
                        <Select v-model="form.goal">
                            <SelectTrigger class="w-full dark:bg-neutral-800">
                                <SelectValue placeholder="Selecione..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="g in goals"
                                    :key="g.value"
                                    :value="g.value"
                                >
                                    {{ g.icon }} {{ g.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.goal" class="text-xs text-red-500">{{ form.errors.goal }}</span>
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <Label class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300">
                            <Timer class="h-3.5 w-3.5 text-orange-500" />
                            Data da Medição
                        </Label>
                        <Input
                            v-model="form.recorded_at"
                            type="date"
                            class="max-w-xs dark:bg-neutral-800"
                        />
                        <span v-if="form.errors.recorded_at" class="text-xs text-red-500">{{ form.errors.recorded_at }}</span>
                    </div>

                    <div class="space-y-1.5 sm:col-span-2">
                        <Label class="flex items-center gap-1.5 text-xs font-medium text-neutral-600 dark:text-neutral-300">
                            <Baby class="h-3.5 w-3.5 text-orange-500" />
                            Observações
                        </Label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Observações sobre a medição..."
                            class="flex w-full rounded-md border border-neutral-200 bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-neutral-400 focus:border-emerald-500 focus:ring-3 focus:ring-emerald-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:placeholder:text-neutral-500"
                        ></textarea>
                        <span v-if="form.errors.notes" class="text-xs text-red-500">{{ form.errors.notes }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 border-t border-neutral-200 bg-white px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex gap-3">
                <Button
                    type="button"
                    variant="outline"
                    class="flex-1 cursor-pointer"
                    @click="handleCancel"
                    :disabled="form.processing"
                >
                    Cancelar
                </Button>
                <Button
                    type="submit"
                    class="flex-1 cursor-pointer bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 hover:brightness-110"
                    :disabled="form.processing"
                >
                    <template v-if="form.processing">Registrando...</template>
                    <template v-else>Registrar Medição</template>
                </Button>
            </div>
        </div>
    </form>
</template>
