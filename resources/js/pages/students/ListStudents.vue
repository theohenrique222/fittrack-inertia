<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { SidebarMenuButton } from '@/components/ui/sidebar';
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

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Alunos',
                href: '/students',
            },
        ],
    },
});
</script>

<template>
    <Head title="Alunos" />

    <div
        v-if="successMessage"
        class="fixed top-4 right-4 rounded-lg bg-green-500 px-4 py-2 text-white shadow-lg"
    >
        {{ successMessage }}
    </div>

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="py-5 font-extrabold">
            <h1>Lista de Alunos</h1>
        </div>

        <div class="flex flex-col gap-4">
            <div
                v-for="student in props.students.data"
                :key="student.id"
                class="flex items-center justify-between rounded-lg border bg-neutral-100 dark:bg-neutral-900 p-4"
            >
                <div class="flex flex-col">
                    <Link :href="`/students/${student.id}`" class="font-semibold text-emerald-600 hover:underline">
                        {{ student.name }}
                    </Link>
                    <p class="text-sm text-neutral-500">{{ student.email }}</p>
                    <p v-if="student.nickname" class="text-sm text-neutral-500">
                        {{ student.nickname }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        size="sm"
                        variant="outline"
                        @click="handleEditClick(student)"
                    >
                        Editar
                    </Button>
                    <Button
                        size="sm"
                        variant="secondary"
                        class="cursor-pointer"
                        @click="handleResetPasswordClick(student.id)"
                    >
                        Redefinir Senha
                    </Button>
                    <Button
                        size="sm"
                        variant="destructive"
                        @click="handleDeleteClick(student.id)"
                    >
                        Deletar
                    </Button>
                </div>
            </div>
        </div>

        <div
            v-if="!props.students.data.length"
            class="py-8 text-center text-neutral-500"
        >
            Nenhum aluno cadastrado
        </div>

        <div v-if="canCreateStudent" class="m-auto mt-4 w-1/2 text-center">
            <Sheet v-model:open="isCreateOpen">
                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="cursor-pointer bg-emerald-500 text-white hover:bg-emerald-600 data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div class="w-full text-center">
                            <span>Cadastrar Aluno</span>
                        </div>
                    </SidebarMenuButton>
                </SheetTrigger>

                <SheetContent @interactOutside.prevent>
                    <SheetHeader>
                        <SheetTitle>Cadastro de Aluno</SheetTitle>
                    </SheetHeader>

                    <div class="px-4">
                        <CreateStudentSheet @change="closeCreateSheet" />
                    </div>
                </SheetContent>
            </Sheet>
        </div>

        <Sheet v-model:open="isEditOpen">
            <SheetContent @interactOutside.prevent>
                <SheetHeader>
                    <SheetTitle>Editar Aluno</SheetTitle>
                </SheetHeader>

                <div class="px-4">
                    <EditStudentSheet
                        v-if="selectedStudent"
                        :student="selectedStudent"
                        @change="closeEditSheet"
                    />
                </div>
            </SheetContent>
        </Sheet>
    </div>
</template>
