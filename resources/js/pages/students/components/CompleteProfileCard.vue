<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Calendar, User, VenusAndMars, ArrowRight } from 'lucide-vue-next';
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

const emit = defineEmits(['saved']);

const form = useForm({
    gender: '',
    birthdate: '',
});

function handleSubmit() {
    form.post('/profile/complete', {
        preserveScroll: true,
        onSuccess: () => emit('saved'),
    });
}
</script>

<template>
    <div class="flex items-center justify-center py-12">
        <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 shadow-2xl">
            <div class="absolute -top-20 -right-20 h-60 w-60 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-blue-500/10 blur-3xl"></div>

            <div class="relative p-8 text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-lg shadow-emerald-500/20">
                    <User class="h-10 w-10 text-white" />
                </div>

                <h2 class="text-2xl font-bold text-white">Complete seu Perfil</h2>
                <p class="mt-2 text-sm text-neutral-300 leading-relaxed">
                    Precisamos de algumas informações suas para calcular
                    suas métricas corporais com precisão.
                </p>

                <form @submit.prevent="handleSubmit" class="mt-8 space-y-5 text-left">
                    <div class="space-y-2">
                        <Label class="flex items-center gap-1.5 text-sm font-medium text-neutral-200">
                            <VenusAndMars class="h-4 w-4 text-emerald-400" />
                            Gênero
                        </Label>
                        <Select v-model="form.gender">
                            <SelectTrigger class="w-full bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-emerald-500 focus:ring-emerald-500/30">
                                <SelectValue placeholder="Selecione seu gênero" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="male">Masculino</SelectItem>
                                <SelectItem value="female">Feminino</SelectItem>
                            </SelectContent>
                        </Select>
                        <span v-if="form.errors.gender" class="text-xs text-red-400">{{ form.errors.gender }}</span>
                        <span v-if="form.errors.profile_incomplete" class="text-xs text-red-400">{{ form.errors.profile_incomplete }}</span>
                    </div>

                    <div class="space-y-2">
                        <Label class="flex items-center gap-1.5 text-sm font-medium text-neutral-200">
                            <Calendar class="h-4 w-4 text-emerald-400" />
                            Data de Nascimento
                        </Label>
                        <Input
                            v-model="form.birthdate"
                            type="date"
                            class="bg-white/10 border-white/20 text-white placeholder:text-neutral-400 focus:border-emerald-500 focus:ring-emerald-500/30 [color-scheme:dark]"
                        />
                        <span v-if="form.errors.birthdate" class="text-xs text-red-400">{{ form.errors.birthdate }}</span>
                    </div>

                    <Button
                        type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/25 transition-all hover:shadow-emerald-500/40 hover:brightness-110"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Salvando...' : 'Salvar Dados' }}
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>
