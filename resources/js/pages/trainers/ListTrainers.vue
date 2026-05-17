<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { SidebarMenuButton } from '@/components/ui/sidebar';
import CreateTrainerSheet from '@/pages/trainers/components/CreateTrainerSheet.vue';
import EditTrainerSheet from '@/pages/trainers/components/EditTrainerSheet.vue';
import { destroy } from '@/routes/trainers';

interface Trainer {
    id: number;
    name: string;
    email: string;
    specialty?: string;
}

const props = defineProps<{
    title: string;
    trainers: {
        data: Trainer[];
    };
}>();

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const selectedTrainer = ref<Trainer | null>(null);

const handleEditClick = (trainer: Trainer) => {
    selectedTrainer.value = trainer;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este treinador?')) {
return;
}

    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedTrainer.value = null;
        },
    });
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Treinadores',
                href: '/trainers',
            },
        ],
    },
});
</script>

<template>
    <Head title="Treinadores" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="py-5 font-extrabold">
            <h1>Lista de Treinadores</h1>
        </div>

        <div class="flex flex-col gap-4 ">
            <div
                v-for="trainer in props.trainers.data"
                :key="trainer.id"
                class="flex items-center justify-between border bg-neutral-100 dark:bg-neutral-900 rounded-lg p-4 shadow-lg"
            >
                <div class="flex flex-col">
                    <h3 class="font-semibold">{{ trainer.name }}</h3>
                    <p class="text-sm text-neutral-500">{{ trainer.email }}</p>
                    <p v-if="trainer.specialty" class="text-sm text-neutral-500">
                        {{ trainer.specialty }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        size="sm"
                        variant="outline"
                        class="cursor-pointer"
                        @click="handleEditClick(trainer)"
                    >
                        Editar
                    </Button>
                    <Button
                        size="sm"
                        variant="destructive"
                        class="cursor-pointer"
                        @click="handleDeleteClick(trainer.id)"
                    >
                        Deletar
                    </Button>
                </div>
            </div>
        </div>

        <div v-if="!props.trainers.data.length" class="text-center text-neutral-500 py-8">
            Nenhum treinador cadastrado
        </div>

        <div class="w-1/2 m-auto text-center mt-4">
            <Sheet v-model:open="isCreateOpen">
                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="cursor-pointer data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground hover:bg-emerald-600 text-white  bg-emerald-500"
                    >
                        <div class="w-full text-center">
                            <span>Cadastrar Treinador</span>
                        </div>
                    </SidebarMenuButton>
                </SheetTrigger>

                <SheetContent @interactOutside.prevent>
                    <SheetHeader>
                        <SheetTitle>Cadastro de Treinador</SheetTitle>
                    </SheetHeader>

                    <div class="px-4">
                        <CreateTrainerSheet @change="closeCreateSheet" />
                    </div>
                </SheetContent>
            </Sheet>
        </div>

        <Sheet v-model:open="isEditOpen">
            <SheetContent @interactOutside.prevent>
                <SheetHeader>
                    <SheetTitle>Editar Treinador</SheetTitle>
                </SheetHeader>

                <div class="px-4">
                    <EditTrainerSheet
                        v-if="selectedTrainer"
                        :trainer="selectedTrainer"
                        @change="closeEditSheet"
                    />
                </div>
            </SheetContent>
        </Sheet>
    </div>
</template>
