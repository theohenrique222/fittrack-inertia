<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import {
    LogOut,
    Shield,
    Palette,
    User as UserIcon,
} from 'lucide-vue-next';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { logout } from '@/routes';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { User } from '@/types';

type Props = {
    user: User;
};

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>

    <DropdownMenuSeparator />

    <DropdownMenuLabel class="text-xs font-medium text-muted-foreground">
        Configurações
    </DropdownMenuLabel>

    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link
                class="flex w-full cursor-pointer items-center gap-2"
                :href="editProfile()"
                prefetch
            >
                <UserIcon class="h-4 w-4" />
                Perfil
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true">
            <Link
                class="flex w-full cursor-pointer items-center gap-2"
                :href="editSecurity()"
                prefetch
            >
                <Shield class="h-4 w-4" />
                Segurança
            </Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child="true">
            <Link
                class="flex w-full cursor-pointer items-center gap-2"
                :href="editAppearance()"
                prefetch
            >
                <Palette class="h-4 w-4" />
                Aparência
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator />

    <DropdownMenuItem :as-child="true" variant="destructive">
        <Link
            class="flex w-full cursor-pointer items-center gap-2"
            :href="logout()"
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <LogOut class="h-4 w-4" />
            Sair
        </Link>
    </DropdownMenuItem>
</template>
