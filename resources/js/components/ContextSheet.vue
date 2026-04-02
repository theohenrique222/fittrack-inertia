<script setup lang="ts">
import { ArrowLeftRight } from 'lucide-vue-next';
import { ref } from 'vue';
import ContextSelector from '@/components/ContextSelector.vue';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';


const isOpen = ref(false);

const closeSheet = () => {
    isOpen.value = false;
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <Sheet v-model:open="isOpen">
                <SheetTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div
                            class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
                        >
                            <ArrowLeftRight class="size-4 dark:text-zinc-900" />
                        </div>
                        <div
                            class="grid flex-1 text-left text-sm leading-tight"
                        >
                            <span class="truncate font-semibold">Contexto</span>
                            <span class="truncate text-xs"
                                >Alterar contexto</span
                            >
                        </div>
                    </SidebarMenuButton>
                </SheetTrigger>
                <SheetContent>
                    <SheetHeader>
                        <SheetTitle>Opções de contexto</SheetTitle>
                    </SheetHeader>

                    <div class="px-4">
                        <ContextSelector @change="closeSheet"/>
                    </div>
                </SheetContent>
            </Sheet>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
