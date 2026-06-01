<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Treinadores',
                href: '/trainers',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    ChevronRight,
    Dumbbell,
    Eye,
    KeyRound,
    Plus,
    Search,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
import CreateTrainerSheet from '@/pages/trainers/components/CreateTrainerSheet.vue';
import EditTrainerSheet from '@/pages/trainers/components/EditTrainerSheet.vue';
import { destroy, impersonate, resetPassword } from '@/routes/trainers';

interface Trainer {
    id: number;
    name: string;
    email: string;
    specialty?: string;
}

const props = defineProps<{
    title: string;
    trainers: Trainer[];
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const canCreateTrainer = page.props.auth.can.create_trainer ?? true;
const canImpersonate = page.props.auth.can.impersonate ?? false;

const searchQuery = ref('');

const filteredTrainers = computed(() => {
    if (!searchQuery.value) {
        return props.trainers;
    }

    const query = searchQuery.value.toLowerCase();

    return props.trainers.filter(
        (t) =>
            t.name.toLowerCase().includes(query) ||
            t.email.toLowerCase().includes(query) ||
            t.specialty?.toLowerCase().includes(query),
    );
});

const stats = computed(() => ({
    total: props.trainers.length,
}));

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
const selectedTrainer = ref<Trainer | null>(null);

const handleEditClick = (trainer: Trainer) => {
    selectedTrainer.value = trainer;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este treinador?')) {
        return;
    }

    router.delete(destroy.url({ trainer: id }), {
        onSuccess: () => {
            selectedTrainer.value = null;
            success('Treinador removido com sucesso.');
        },
    });
};

const handleImpersonateClick = (trainer: Trainer) => {
    if (!confirm(`Deseja personificar ${trainer.name}?`)) {
        return;
    }

    router.post(impersonate.url({ trainer: trainer.id }));
};

const handleResetPasswordClick = (id: number) => {
    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) {
        return;
    }

    router.post(resetPassword.url({ trainer: id }));
};

const closeCreateSheet = () => {
    isCreateOpen.value = false;
};

const closeEditSheet = () => {
    isEditOpen.value = false;
};

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

const avatarColors = [
    'from-emerald-400 to-emerald-600',
    'from-teal-400 to-teal-600',
    'from-cyan-400 to-cyan-600',
    'from-green-400 to-green-600',
    'from-lime-400 to-lime-600',
];

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}
</script>

<template>
    <Head title="Treinadores" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white"
        >
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Treinadores</h1>
                    <p class="mt-1 text-sm text-emerald-100">
                        Gerencie seus treinadores cadastrados
                    </p>
                </div>

                <Sheet v-if="canCreateTrainer" v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button
                            class="border-0 bg-white text-emerald-700 shadow-lg hover:bg-emerald-50"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Novo Treinador
                        </Button>
                    </SheetTrigger>

                    <SheetContent side="right" class="sm:max-w-md">
                        <SheetHeader>
                            <SheetTitle>Cadastro de Treinador</SheetTitle>
                        </SheetHeader>

                        <CreateTrainerSheet @change="closeCreateSheet" />
                    </SheetContent>
                </Sheet>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <Users class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Total</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <UserPlus class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100">Ativos</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <Dumbbell class="h-4 w-4 text-emerald-100" />
                        <span class="text-xs text-emerald-100"
                            >Cadastrados</span
                        >
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
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
                        placeholder="Buscar treinador por nome, email ou especialidade..."
                        class="border-neutral-200 bg-white pl-9 dark:border-neutral-700 dark:bg-neutral-800"
                    />
                </div>
            </div>

            <div v-if="filteredTrainers.length" class="space-y-3">
                <div
                    v-for="trainer in filteredTrainers"
                    :key="trainer.id"
                    class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-emerald-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                >
                    <div class="flex items-center gap-4 p-4">
                        <div
                            :class="[
                                'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-sm font-bold text-white shadow-sm',
                                getAvatarColor(trainer.id),
                            ]"
                        >
                            {{ getInitials(trainer.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3
                                    class="truncate font-semibold text-neutral-900 dark:text-white"
                                >
                                    {{ trainer.name }}
                                </h3>
                                <span
                                    v-if="trainer.specialty"
                                    class="inline-flex shrink-0 items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                                >
                                    {{ trainer.specialty }}
                                </span>
                            </div>
                            <p
                                class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                            >
                                {{ trainer.email }}
                            </p>
                        </div>

                        <ChevronRight
                            class="h-5 w-5 shrink-0 text-neutral-300 transition-colors group-hover:text-emerald-500 dark:text-neutral-600"
                        />
                    </div>

                    <div
                        class="flex items-center justify-end gap-1 border-t border-neutral-100 px-4 py-2 dark:border-neutral-700"
                    >
                        <Button
                            v-if="canImpersonate"
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleImpersonateClick(trainer)"
                        >
                            <Eye class="mr-1 h-3.5 w-3.5 text-amber-500" />
                            Personificar
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleResetPasswordClick(trainer.id)"
                        >
                            <KeyRound class="mr-1 h-3.5 w-3.5 text-blue-500" />
                            Redefinir Senha
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleEditClick(trainer)"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-1 h-3.5 w-3.5 text-neutral-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"
                                />
                                <path d="m15 5 4 4" />
                            </svg>
                            Editar
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleDeleteClick(trainer.id)"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mr-1 h-3.5 w-3.5 text-red-500"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M3 6h18" />
                                <path
                                    d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"
                                />
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                <line x1="10" x2="10" y1="11" y2="17" />
                                <line x1="14" x2="14" y1="11" y2="17" />
                            </svg>
                            Deletar
                        </Button>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <div
                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800"
                >
                    <Users
                        class="h-8 w-8 text-neutral-300 dark:text-neutral-600"
                    />
                </div>
                <p class="font-medium text-neutral-600 dark:text-neutral-400">
                    {{
                        searchQuery
                            ? 'Nenhum treinador encontrado'
                            : 'Nenhum treinador cadastrado'
                    }}
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    {{
                        searchQuery
                            ? 'Tente buscar com outros termos'
                            : 'Clique em "Novo Treinador" para começar'
                    }}
                </p>
            </div>
        </div>
    </div>

    <Sheet v-model:open="isEditOpen">
        <SheetContent side="right" class="sm:max-w-md">
            <SheetHeader>
                <SheetTitle>Editar Treinador</SheetTitle>
            </SheetHeader>

            <EditTrainerSheet
                v-if="selectedTrainer"
                :trainer="selectedTrainer"
                @close="closeEditSheet"
            />
        </SheetContent>
    </Sheet>
</template>
