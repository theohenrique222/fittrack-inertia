<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ArrowLeft, Eye } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { stopImpersonate } from '@/routes/stop-impersonate';

const page = usePage();

const impersonating = computed(() => page.props.auth.impersonating ?? false);
const impersonatorName = computed(() => page.props.auth.impersonator_name ?? '');

function handleStopImpersonate() {
    stopImpersonate.post();
}
</script>

<template>
    <div
        v-if="impersonating"
        class="flex items-center justify-center gap-3 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 px-4 py-2.5 text-sm font-medium text-amber-950 shadow-sm"
    >
        <Eye class="h-4 w-4 shrink-0" />
        <span>
            Você está personificando
            <strong class="font-semibold">{{ impersonatorName }}</strong>
        </span>
        <Button
            variant="outline"
            size="sm"
            class="ml-2 h-7 gap-1.5 border-amber-600 bg-white/80 text-amber-900 hover:bg-white hover:text-amber-950"
            @click="handleStopImpersonate"
        >
            <ArrowLeft class="h-3.5 w-3.5" />
            Voltar
        </Button>
    </div>
</template>
