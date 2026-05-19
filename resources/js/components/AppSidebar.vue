<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { LayoutGrid, Dumbbell, BarChart3 } from 'lucide-vue-next';
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
import { students, dashboard, exercises, trainers, reports } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

const can = page.props.auth.can;

const mainNavItems: NavItem[] = [
    {
        title: 'Painel de Controle',
        href: dashboard(),
        icon: LayoutGrid,
    },

    ...(can.view_students
        ? [
              {
                  title: 'Alunos',
                  href: students(),
                  icon: LayoutGrid,
              },
          ]
        : []),

    ...(can.view_trainers
        ? [
              {
                  title: 'Treinadores',
                  href: trainers(),
                  icon: LayoutGrid,
              },
          ]
        : []),

    {
        title: 'Exercícios',
        href: exercises(),
        icon: Dumbbell,
    },

    ...(can.view_students
        ? [
              {
                  title: 'Relatórios',
                  href: reports(),
                  icon: BarChart3,
              },
          ]
        : []),
];
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
