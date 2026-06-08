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
    Edit,
    Eye,
    KeyRound,
    Plus,
    Search,
    Trash2,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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

const handleEditClick = (trainer: Trainer, event: Event) => {
    event.stopPropagation();
    selectedTrainer.value = trainer;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number, event: Event) => {
    event.stopPropagation();

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

const handleImpersonateClick = (trainer: Trainer, event: Event) => {
    event.stopPropagation();

    if (!confirm(`Deseja personificar ${trainer.name}?`)) {
        return;
    }

    router.post(impersonate.url({ trainer: trainer.id }));
};

const handleResetPasswordClick = (id: number, event: Event) => {
    event.stopPropagation();

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

    <div class="flex h-full flex-1 flex-col rounded-xl">
        <div
            class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-4 py-6 text-white sm:px-6 sm:py-8"
        >
            <div class="mb-6 flex items-center justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold sm:text-3xl">Treinadores</h1>
                    <p class="mt-1 text-sm text-emerald-100">
                        Gerencie seus treinadores cadastrados
                    </p>
                </div>

                <Sheet v-if="canCreateTrainer" v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button
                            class="shrink-0 border-0 bg-white text-emerald-700 shadow-lg hover:bg-emerald-50"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            <span class="hidden sm:inline">Novo Treinador</span>
                            <span class="sm:hidden">Novo</span>
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

            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                <div class="rounded-xl bg-white/15 px-3 py-3 backdrop-blur-sm sm:px-5 sm:py-4">
                    <div class="mb-1 flex items-center gap-2 sm:mb-2">
                        <Users class="h-4 w-4 text-emerald-100 sm:h-5 sm:w-5" />
                        <span class="text-xs text-emerald-100 sm:text-sm sm:font-medium">Total</span>
                    </div>
                    <p class="text-xl font-bold sm:text-3xl">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-3 py-3 backdrop-blur-sm sm:px-5 sm:py-4">
                    <div class="mb-1 flex items-center gap-2 sm:mb-2">
                        <UserPlus class="h-4 w-4 text-emerald-100 sm:h-5 sm:w-5" />
                        <span class="text-xs text-emerald-100 sm:text-sm sm:font-medium">Ativos</span>
                    </div>
                    <p class="text-xl font-bold sm:text-3xl">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-3 py-3 backdrop-blur-sm sm:px-5 sm:py-4">
                    <div class="mb-1 flex items-center gap-2 sm:mb-2">
                        <Dumbbell class="h-4 w-4 text-emerald-100 sm:h-5 sm:w-5" />
                        <span class="text-xs text-emerald-100 sm:text-sm sm:font-medium">Cadastrados</span>
                    </div>
                    <p class="text-xl font-bold sm:text-3xl">{{ stats.total }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 px-4 py-5 dark:bg-neutral-900 sm:px-6">
            <div class="mb-6">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-neutral-400"
                    />
                    <Input
                        v-model="searchQuery"
                        placeholder="Buscar treinador por nome, email ou especialidade..."
                        class="border-neutral-200 bg-white pl-12 py-6 text-base dark:border-neutral-700 dark:bg-neutral-800"
                    />
                </div>
            </div>

            <div v-if="filteredTrainers.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="trainer in filteredTrainers"
                    :key="trainer.id"
                    class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-emerald-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                >
                    <div class="border-b border-neutral-100 bg-gradient-to-r from-neutral-50 to-white p-5 dark:border-neutral-700 dark:from-neutral-900/50 dark:to-neutral-800">
                        <div class="flex items-center gap-4">
                            <div
                                :class="[
                                    'flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-xl font-bold text-white shadow-md',
                                    getAvatarColor(trainer.id),
                                ]"
                            >
                                {{ getInitials(trainer.name) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="truncate text-lg font-semibold text-neutral-900 dark:text-white"
                                    >
                                        {{ trainer.name }}
                                    </h3>
                                </div>
                                <span
                                    v-if="trainer.specialty"
                                    class="mt-1 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                                >
                                    {{ trainer.specialty }}
                                </span>
                            </div>

                            <ChevronRight
                                class="h-5 w-5 shrink-0 text-neutral-300 transition-all group-hover:translate-x-1 group-hover:text-emerald-500 dark:text-neutral-600"
                            />
                        </div>
                    </div>

                    <div class="p-5">
                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400">
                            {{ trainer.email }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-1 border-t border-neutral-100 px-4 py-3 dark:border-neutral-700">
                        <Button
                            v-if="canImpersonate"
                            size="sm"
                            variant="ghost"
                            class="h-9 px-3 text-xs"
                            @click.stop="handleImpersonateClick(trainer, $event)"
                        >
                            <Eye class="mr-1.5 h-4 w-4 text-amber-500" />
                            Personificar
                        </Button>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button size="sm" variant="ghost" class="h-9 w-9 p-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-500">
                                        <circle cx="12" cy="12" r="1"/>
                                        <circle cx="12" cy="5" r="1"/>
                                        <circle cx="12" cy="19" r="1"/>
                                    </svg>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click.stop="handleEditClick(trainer, $event)">
                                    <Edit class="mr-2 h-4 w-4" />
                                    Editar
                                </DropdownMenuItem>
                                <DropdownMenuItem @click.stop="handleResetPasswordClick(trainer.id, $event)">
                                    <KeyRound class="mr-2 h-4 w-4" />
                                    Redefinir Senha
                                </DropdownMenuItem>
                                <DropdownMenuItem class="text-red-600" @click.stop="handleDeleteClick(trainer.id, $event)">
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Deletar
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center py-20 text-center"
            >
                <div
                    class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800"
                >
                    <Users
                        class="h-10 w-10 text-neutral-300 dark:text-neutral-600"
                    />
                </div>
                <p class="text-lg font-semibold text-neutral-600 dark:text-neutral-400">
                    {{
                        searchQuery
                            ? 'Nenhum treinador encontrado'
                            : 'Nenhum treinador cadastrado'
                    }}
                </p>
                <p class="mt-2 text-sm text-neutral-400 dark:text-neutral-500">
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
