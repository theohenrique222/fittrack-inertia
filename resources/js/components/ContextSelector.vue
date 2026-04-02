<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const page = usePage();

const availableContexts = page.props.context?.availableContexts ?? {};
const currentContext = page.props.context?.currentContext ?? null;

const handleContextChange = (newContext: string | null) => {
    if (!newContext) return;

    router.post('/change-context', {
        context: newContext,
    });
};

</script>

<template>
    <div>
        <Label class="mb-2">Selecionar contexto</Label>

        <Select @update:model-value="(v) => handleContextChange(v as string)">
            <SelectTrigger>
                <SelectValue :placeholder="'Selecione um contexto'" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="(label, value) in availableContexts"
                    :key="value"
                    :value="value"
                >
                    {{ label }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
