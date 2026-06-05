<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Todos os Alunos',
                href: '/admin/students',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    Archive,
    RotateCcw,
    Search,
    Trash2,
    Users,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ToastContainer } from '@/components/ui/toast';
import { useToast } from '@/composables/useToast';
import { restore, forceDelete } from '@/routes/students';

interface Student {
    id: number;
    name: string;
    email: string;
    nickname?: string;
    trainer_name?: string;
    deleted_at: string | null;
}

const props = defineProps<{
    title: string;
    students: Student[];
    archivedStudents: Student[];
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const activeTab = ref<'active' | 'archived'>('active');
const searchQuery = ref('');

const currentList = computed(() =>
    activeTab.value === 'active' ? props.students : props.archivedStudents,
);

const filteredStudents = computed(() => {
    if (!searchQuery.value) {
        return currentList.value;
    }

    const query = searchQuery.value.toLowerCase();

    return currentList.value.filter(
        (s) =>
            s.name.toLowerCase().includes(query) ||
            s.email.toLowerCase().includes(query) ||
            s.nickname?.toLowerCase().includes(query) ||
            s.trainer_name?.toLowerCase().includes(query),
    );
});

const stats = computed(() => ({
    total: props.students.length,
    archived: props.archivedStudents.length,
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

const handleRestoreClick = (id: number, event: Event) => {
    event.stopPropagation();

    if (!confirm('Tem certeza que deseja restaurar este aluno?')) {
        return;
    }

    router.post(restore.url({ student: id }));
};

const handleForceDeleteClick = (id: number, event: Event) => {
    event.stopPropagation();

    if (!confirm('Tem certeza que deseja excluir permanentemente este aluno? Esta ação não pode ser desfeita.')) {
        return;
    }

    router.delete(forceDelete.url({ student: id }));
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
    'from-blue-400 to-blue-600',
    'from-violet-400 to-violet-600',
    'from-orange-400 to-orange-600',
    'from-pink-400 to-pink-600',
    'from-cyan-400 to-cyan-600',
];

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}
</script>

<template>
    <Head title="Todos os Alunos" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-700 px-6 py-8 text-white">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Todos os Alunos</h1>
                <p class="mt-1 text-sm text-purple-100">
                    Visualização geral de todos os alunos da plataforma
                </p>
            </div>

            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                    <div class="mb-2 flex items-center gap-2">
                        <Users class="h-5 w-5 text-purple-100" />
                        <span class="text-sm font-medium text-purple-100">Ativos</span>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                    <div class="mb-2 flex items-center gap-2">
                        <Archive class="h-5 w-5 text-purple-100" />
                        <span class="text-sm font-medium text-purple-100">Arquivados</span>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.archived }}</p>
                </div>
            </div>
        </div>

        <div class="flex-1 bg-neutral-50 px-6 py-5 dark:bg-neutral-900">
            <div class="mb-6 flex items-center gap-4">
                <div class="relative flex-1">
                    <Search class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-neutral-400" />
                    <Input
                        v-model="searchQuery"
                        :placeholder="activeTab === 'active' ? 'Buscar aluno por nome, email, apelido ou personal...' : 'Buscar aluno arquivado...'"
                        class="border-neutral-200 bg-white pl-12 py-6 text-base dark:border-neutral-700 dark:bg-neutral-800"
                    />
                </div>

                <div class="flex rounded-lg border border-neutral-200 bg-white p-1 dark:border-neutral-700 dark:bg-neutral-800">
                    <Button
                        size="sm"
                        :variant="activeTab === 'active' ? 'default' : 'ghost'"
                        @click="activeTab = 'active'"
                    >
                        <Users class="mr-1.5 h-4 w-4" />
                        Ativos
                    </Button>
                    <Button
                        size="sm"
                        :variant="activeTab === 'archived' ? 'default' : 'ghost'"
                        @click="activeTab = 'archived'"
                    >
                        <Archive class="mr-1.5 h-4 w-4" />
                        Arquivados
                    </Button>
                </div>
            </div>

            <div v-if="filteredStudents.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="student in filteredStudents"
                    :key="student.id"
                    class="group cursor-default overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800"
                    :class="activeTab === 'archived' ? 'border-amber-200 hover:border-amber-300 dark:border-amber-800 dark:hover:border-amber-600' : 'hover:border-purple-300 dark:hover:border-purple-600'"
                >
                    <div class="border-b border-neutral-100 bg-gradient-to-r from-neutral-50 to-white p-5 dark:border-neutral-700 dark:from-neutral-900/50 dark:to-neutral-800">
                        <div class="flex items-center gap-4">
                            <div :class="[
                                'flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-xl font-bold text-white shadow-md',
                                getAvatarColor(student.id),
                            ]">
                                {{ getInitials(student.name) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="truncate text-lg font-semibold text-neutral-900 dark:text-white">
                                        {{ student.name }}
                                    </h3>
                                </div>
                                <p v-if="student.nickname" class="mt-1 text-sm text-purple-600 dark:text-purple-400">
                                    {{ student.nickname }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400">
                            {{ student.email }}
                        </p>
                        <p v-if="student.trainer_name" class="mt-2 truncate text-sm text-neutral-500 dark:text-neutral-400">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">Personal:</span> {{ student.trainer_name }}
                        </p>
                        <p v-else class="mt-2 text-sm text-neutral-400 dark:text-neutral-500 italic">
                            Sem personal vinculado
                        </p>
                    </div>

                    <div v-if="activeTab === 'archived'" class="flex items-center justify-end gap-2 border-t border-neutral-100 px-4 py-3 dark:border-neutral-700">
                        <Button
                            size="sm"
                            variant="outline"
                            class="border-emerald-200 text-emerald-700 hover:bg-emerald-50 dark:border-emerald-800 dark:text-emerald-400"
                            @click.stop="handleRestoreClick(student.id, $event)"
                        >
                            <RotateCcw class="mr-1.5 h-4 w-4" />
                            Restaurar
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            class="border-red-200 text-red-600 hover:bg-red-50 dark:border-red-800 dark:text-red-400"
                            @click.stop="handleForceDeleteClick(student.id, $event)"
                        >
                            <Trash2 class="mr-1.5 h-4 w-4" />
                            Excluir
                        </Button>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800">
                    <component :is="activeTab === 'archived' ? Archive : Users" class="h-10 w-10 text-neutral-300 dark:text-neutral-600" />
                </div>
                <p class="text-lg font-semibold text-neutral-600 dark:text-neutral-400">
                    {{ searchQuery ? 'Nenhum aluno encontrado' : (activeTab === 'archived' ? 'Nenhum aluno arquivado' : 'Nenhum aluno cadastrado') }}
                </p>
                <p class="mt-2 text-sm text-neutral-400 dark:text-neutral-500">
                    {{ searchQuery ? 'Tente buscar com outros termos' : '' }}
                </p>
            </div>
        </div>
    </div>
</template>
