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
    Edit,
    Trash2,
    KeyRound,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
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
    students: Student[];
}>();

const page = usePage();
const { toasts, success, error } = useToast();

const canCreateStudent = page.props.auth.can.create_student;

const searchQuery = ref('');

const filteredStudents = computed(() => {
    if (!searchQuery.value) {
        return props.students;
    }

    const query = searchQuery.value.toLowerCase();

    return props.students.filter(
        (s) =>
            s.name.toLowerCase().includes(query) ||
            s.email.toLowerCase().includes(query) ||
            s.nickname?.toLowerCase().includes(query),
    );
});

const stats = computed(() => ({
    total: props.students.length,
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

const handleEditClick = (student: Student, event: Event) => {
    event.stopPropagation();
    selectedStudent.value = student;
    isEditOpen.value = true;
};

const handleDeleteClick = (id: number, event: Event) => {
    event.stopPropagation();

    if (!confirm('Tem certeza que deseja arquivar este aluno? Ele poderá ser restaurado posteriormente por um administrador.')) {
        return;
    }

    router.delete(destroy.url({ student: id }), {
        onSuccess: () => {
            selectedStudent.value = null;
        },
    });
};

const handleResetPasswordClick = (id: number, event: Event) => {
    event.stopPropagation();

    if (!confirm('Tem certeza que deseja redefinir a senha para "password"?')) {
        return;
    }

    router.post(resetPassword.url({ student: id }));
};

const handleCreateWorkoutClick = (id: number, event: Event) => {
    event.stopPropagation();
    router.visit(`/students/${id}`);
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
    <Head title="Alunos" />

    <ToastContainer v-model="toasts" />

    <div class="flex h-full flex-1 flex-col overflow-x-auto rounded-xl">
        <!-- Header com gradiente -->
        <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 px-6 py-8 text-white">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Alunos</h1>
                    <p class="mt-1 text-sm text-emerald-100">
                        Gerencie seus alunos e acompanhe seu progresso
                    </p>
                </div>

                <Sheet v-if="canCreateStudent" v-model:open="isCreateOpen">
                    <SheetTrigger as-child>
                        <Button class="border-0 bg-white text-emerald-700 shadow-lg hover:bg-emerald-50">
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

            <!-- Stats Cards -->
            <div class="grid grid-cols-3 gap-4">
                <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                    <div class="mb-2 flex items-center gap-2">
                        <Users class="h-5 w-5 text-emerald-100" />
                        <span class="text-sm font-medium text-emerald-100">Total</span>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                    <div class="mb-2 flex items-center gap-2">
                        <UserPlus class="h-5 w-5 text-emerald-100" />
                        <span class="text-sm font-medium text-emerald-100">Ativos</span>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.total }}</p>
                </div>

                <div class="rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                    <div class="mb-2 flex items-center gap-2">
                        <GraduationCap class="h-5 w-5 text-emerald-100" />
                        <span class="text-sm font-medium text-emerald-100">Cadastrados</span>
                    </div>
                    <p class="text-3xl font-bold">{{ stats.total }}</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 bg-neutral-50 px-6 py-5 dark:bg-neutral-900">
            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <Search class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-neutral-400" />
                    <Input
                        v-model="searchQuery"
                        placeholder="Buscar aluno por nome, email ou apelido..."
                        class="border-neutral-200 bg-white pl-12 py-6 text-base dark:border-neutral-700 dark:bg-neutral-800"
                    />
                </div>
            </div>

            <!-- Student Cards -->
            <div v-if="filteredStudents.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="student in filteredStudents"
                    :key="student.id"
                    class="group cursor-pointer overflow-hidden rounded-xl border border-neutral-200 bg-white transition-all hover:border-emerald-300 hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-emerald-600"
                    @click="router.visit(`/students/${student.id}`)"
                >
                    <!-- Card Header -->
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
                                <Badge v-if="student.nickname" variant="secondary" class="mt-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    {{ student.nickname }}
                                </Badge>
                            </div>

                            <ChevronRight class="h-5 w-5 shrink-0 text-neutral-300 transition-all group-hover:translate-x-1 group-hover:text-emerald-500 dark:text-neutral-600" />
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5">
                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400">
                            {{ student.email }}
                        </p>
                    </div>

                    <!-- Card Actions -->
                    <div class="flex items-center justify-end gap-1 border-t border-neutral-100 px-4 py-3 dark:border-neutral-700">
                        <Button
                            size="sm"
                            variant="ghost"
                            class="h-9 px-3 text-xs"
                            @click.stop="handleCreateWorkoutClick(student.id, $event)"
                        >
                            <Dumbbell class="mr-1.5 h-4 w-4 text-emerald-600" />
                            Treino
                        </Button>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child @click.stop>
                                <Button size="sm" variant="ghost" class="h-9 w-9 p-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-neutral-500">
                                        <circle cx="12" cy="12" r="1"/>
                                        <circle cx="12" cy="5" r="1"/>
                                        <circle cx="12" cy="19" r="1"/>
                                    </svg>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem @click.stop="handleEditClick(student, $event)">
                                    <Edit class="mr-2 h-4 w-4" />
                                    Editar
                                </DropdownMenuItem>
                                <DropdownMenuItem @click.stop="handleResetPasswordClick(student.id, $event)">
                                    <KeyRound class="mr-2 h-4 w-4" />
                                    Redefinir Senha
                                </DropdownMenuItem>
                                <DropdownMenuItem class="text-red-600" @click.stop="handleDeleteClick(student.id, $event)">
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Deletar
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800">
                    <Users class="h-10 w-10 text-neutral-300 dark:text-neutral-600" />
                </div>
                <p class="text-lg font-semibold text-neutral-600 dark:text-neutral-400">
                    {{ searchQuery ? 'Nenhum aluno encontrado' : 'Nenhum aluno cadastrado' }}
                </p>
                <p class="mt-2 text-sm text-neutral-400 dark:text-neutral-500">
                    {{ searchQuery ? 'Tente buscar com outros termos' : 'Clique em "Novo Aluno" para começar' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Edit Sheet -->
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
