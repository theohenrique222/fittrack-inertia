<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Dumbbell, BarChart3, GraduationCap, Ruler, Users, Play, CreditCard } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import ContextSheet from '@/components/ContextSheet.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { students, dashboard, exercises, trainers, reports, measurements, plans } from '@/routes';
import { students as adminStudents } from '@/routes/admin';
import { workouts } from '@/routes/me';
import type { NavItem } from '@/types';

const page = usePage();

const can = computed(() => page.props.auth.can);
const user = computed(() => page.props.auth.user);
const clientId = computed(() => page.props.auth.client_id);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Painel de Controle',
        href: dashboard(),
        icon: LayoutGrid,
    },

    ...(can.value?.view_students && !can.value?.impersonate
        ? [
              {
                  title: 'Alunos',
                  href: students(),
                  icon: LayoutGrid,
              },
          ]
        : []),

    ...(can.value?.view_trainers
        ? [
              {
                  title: 'Treinadores',
                  href: trainers(),
                  icon: Users,
              },
          ]
        : []),

    ...(can.value?.impersonate
        ? [
              {
                  title: 'Todos os Alunos',
                  href: adminStudents.url(),
                  icon: GraduationCap,
              },
          ]
        : []),

    ...(can.value?.view_students || can.value?.view_trainers
        ? [
              {
                  title: 'Exercícios',
                  href: exercises(),
                  icon: Dumbbell,
              },
          ]
        : []),

    ...(can.value?.view_students
        ? [
              {
                  title: 'Relatórios',
                  href: reports(),
                  icon: BarChart3,
              },
          ]
        : []),

    ...(can.value?.view_students
        ? [
              {
                  title: 'Planos',
                  href: plans(),
                  icon: CreditCard,
              },
          ]
        : []),

    ...(user.value?.role === 'client'
        ? [
              {
                  title: 'Meus Treinos',
                  href: workouts(),
                  icon: Play,
              },
          ]
        : []),

    ...(can.value?.view_students || user.value?.role === 'client'
        ? [
              {
                  title: 'Medidas',
                  href: user.value?.role === 'client' && clientId.value
                      ? `/students/${clientId.value}/measurements`
                      : measurements(),
                  icon: Ruler,
              },
          ]
        : []),
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                        <Link :href="students()" />
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <ContextSheet />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
