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
    Dumbbell,
    GraduationCap,
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
const { toasts, success, error } = useToast();

const canCreateStudent = page.props.auth.can.create_student;

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
const selectedStudent = ref<Student | null>(null);

const handleEditClick = (student: Student) => {
    selectedStudent.value = student;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number) => {
    if (!confirm('Tem certeza que deseja deletar este aluno?')) {
        return;
    }

    router.delete(destroy.url({ student: id }), {
        onSuccess: () => {
            selectedStudent.value = null;
        },
    });
};

const handleResetPasswordClick = (id: number) => {
    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) {
        return;
    }

    router.post(resetPassword.url({ student: id }));
};

const handleCreateWorkoutClick = (id: number, event: Event) => {
    event.stopPropagation();
    router.visit(`/workouts?student_id=${id}&create=true`);
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
    'from-blue-400 to-blue-600',
    'from-indigo-400 to-indigo-600',
    'from-violet-400 to-violet-600',
    'from-purple-400 to-purple-600',
    'from-sky-400 to-sky-600',
];

function getAvatarColor(id: number): string {
    return avatarColors[id % avatarColors.length];
}
</script>

<template>
    <Head title="Alunos" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <div
            class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 px-6 py-8 text-white"
        >
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Alunos</h1>
                    <p class="mt-1 text-sm text-blue-100">
                        Gerencie seus alunos cadastrados
                    </p>
                </div>

                <Sheet v-if="canCreateStudent" v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button
                            class="border-0 bg-white text-blue-700 shadow-lg hover:bg-blue-50"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Novo Aluno
                        </Button>
                    </SheetTrigger>

                    <SheetContent side="right" class="sm:max-w-md">
                        <SheetHeader>
                            <SheetTitle>Cadastro de Aluno</SheetTitle>
                        </SheetHeader>

                        <CreateStudentSheet @change="closeCreateSheet" />
                    </SheetContent>
                </Sheet>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <Users class="h-4 w-4 text-blue-100" />
                        <span class="text-xs text-blue-100">Total</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <UserPlus class="h-4 w-4 text-blue-100" />
                        <span class="text-xs text-blue-100">Ativos</span>
                    </div>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-4 py-3 backdrop-blur-sm">
                    <div class="mb-1 flex items-center gap-2">
                        <GraduationCap class="h-4 w-4 text-blue-100" />
                        <span class="text-xs text-blue-100"
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
                    class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-blue-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-600"
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
                                    class="inline-flex shrink-0 items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
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
                            class="h-5 w-5 shrink-0 text-neutral-300 transition-colors group-hover:text-blue-500 dark:text-neutral-600"
                        />
                    </div>

                    <div
                        class="flex items-center justify-end gap-1 border-t border-neutral-100 px-4 py-2 dark:border-neutral-700"
                    >
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-8 px-3 text-xs"
                            @click.stop="handleCreateWorkoutClick(student.id, $event)"
                        >
                            <Dumbbell class="mr-1 h-3.5 w-3.5 text-emerald-600" />
                            Novo Treino
                        </Button>
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

    <Sheet v-model:open="isEditOpen">
        <SheetContent side="right" class="sm:max-w-md">
            <SheetHeader>
                <SheetTitle>Editar Aluno</SheetTitle>
            </SheetHeader>

            <EditStudentSheet
                v-if="selectedStudent"
                :student="selectedStudent"
                @close="closeEditSheet"
            />
        </SheetContent>
    </Sheet>
</template>
