<script lang="ts">
export default {
    layout: {
        breadcrumbs: [
            {
                title: 'Alunos',
                href: '/students',
            },
        ],
    },
};
</script>

<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    ChevronRight,
    Mail,
    Plus,
    Search,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import CreateStudentSheet from '@/pages/students/components/CreateStudentSheet.vue';
import EditStudentSheet from '@/pages/students/components/EditStudentSheet.vue';
import { destroy, resetPassword } from '@/routes/students';

interface Student {
    id: number;
    name: string;
    email: string;
    nickname?: string;
}

const props = defineProps<{
    title: string;
    students: {
        data: Student[];
    };
}>();

const page = usePage();

const canCreateStudent = page.props.auth.can.create_client;

const successMessage = ref('');
const searchQuery = ref('');

const filteredStudents = computed(() => {
    if (!searchQuery.value) {
        return props.students.data;
    }

    const query = searchQuery.value.toLowerCase();

    return props.students.data.filter(
        (s) =>
            s.name.toLowerCase().includes(query) ||
            s.email.toLowerCase().includes(query) ||
            s.nickname?.toLowerCase().includes(query),
    );
});

const stats = computed(() => ({
    total: props.students.data.length,
}));

watch(
    () => page.props.flash,
    (flash: any) => {
        if (flash?.success) {
            successMessage.value = flash.success;
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        }
    },
    { immediate: true },
);

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const selectedStudent = ref<Student | null>(null);

const handleEditClick = (student: Student) => {
    selectedStudent.value = student;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este aluno?')) {
        return;
    }

    router.delete(destroy.url(id), {
        onSuccess: () => {
            selectedStudent.value = null;
        },
    });
};

const handleResetPasswordClick = (id: number) => {
    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) {
        return;
    }

    router.post(resetPassword.url(id), {});
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
    <Head title="Alunos" />

    <div
        v-if="successMessage"
        class="fixed top-4 right-4 z-50 rounded-lg bg-emerald-500 px-4 py-2 text-white shadow-lg"
    >
        {{ successMessage }}
    </div>

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 px-6 py-8 text-white"
        >
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Alunos</h1>
                    <p class="mt-1 text-sm text-emerald-100">
                        Gerencie seus alunos cadastrados
                    </p>
                </div>

                <Dialog v-if="canCreateStudent" v-model:open="isCreateOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="border-0 bg-white text-emerald-700 shadow-lg hover:bg-emerald-50"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Novo Aluno
                        </Button>
                    </DialogTrigger>

                    <DialogContent class="max-h-[90vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>Cadastro de Aluno</DialogTitle>
                        </DialogHeader>

                        <CreateStudentSheet @change="closeCreateSheet" />
                    </DialogContent>
                </Dialog>
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
                        <Mail class="h-4 w-4 text-emerald-100" />
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
                        placeholder="Buscar aluno por nome, email ou apelido..."
                        class="border-neutral-200 bg-white pl-9 dark:border-neutral-700 dark:bg-neutral-800"
                    />
                </div>
            </div>

            <div v-if="filteredStudents.length" class="space-y-3">
                <div
                    v-for="student in filteredStudents"
                    :key="student.id"
                    class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-emerald-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                    @click="router.visit(`/students/${student.id}`)"
                >
                    <div class="flex items-center gap-4 p-4">
                        <div
                            :class="[
                                'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-sm font-bold text-white shadow-sm',
                                getAvatarColor(student.id),
                            ]"
                        >
                            {{ getInitials(student.name) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3
                                    class="truncate font-semibold text-neutral-900 dark:text-white"
                                >
                                    {{ student.name }}
                                </h3>
                                <span
                                    v-if="student.nickname"
                                    class="inline-flex shrink-0 items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                                >
                                    {{ student.nickname }}
                                </span>
                            </div>
                            <p
                                class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                            >
                                {{ student.email }}
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
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleEditClick(student)"
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
                            @click.stop="handleResetPasswordClick(student.id)"
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
                                <rect
                                    width="18"
                                    height="11"
                                    x="3"
                                    y="11"
                                    rx="2"
                                    ry="2"
                                />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Redefinir Senha
                        </Button>
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleDeleteClick(student.id)"
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
                            ? 'Nenhum aluno encontrado'
                            : 'Nenhum aluno cadastrado'
                    }}
                </p>
                <p class="mt-1 text-sm text-neutral-400 dark:text-neutral-500">
                    {{
                        searchQuery
                            ? 'Tente buscar com outros termos'
                            : 'Clique em "Novo Aluno" para começar'
                    }}
                </p>
            </div>
        </div>
    </div>

    <Dialog v-model:open="isEditOpen">
        <DialogContent class="max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Aluno</DialogTitle>
            </DialogHeader>

            <EditStudentSheet
                v-if="selectedStudent"
                :student="selectedStudent"
                @close="closeEditSheet"
            />
        </DialogContent>
    </Dialog>
</template>
