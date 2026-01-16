<script setup lang="ts">
import NavMain from '@/components/custom/NavMain.vue';
import NavUser from '@/components/custom/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { LayoutDashboard, Users, Shell } from 'lucide-vue-next';

interface MainNavigationItem {
    title: string;
    url: string;
    icon: any;
    items?: {
        title: string;
        url: string;
    }[];
}

const navMain: MainNavigationItem[] = [
    {
        title: '仪表板',
        url: '/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: '用户管理',
        url: '/user',
        icon: Users,
    },
];
</script>

<template>
    <Sidebar variant="inset" collapsible="icon">
        <SidebarHeader>
            <div class="flex items-center gap-2 px-2 py-1">
                <Shell class="size-5" />
                <span class="font-semibold truncate group-data-[collapsible=icon]:hidden">启动模板</span>
            </div>
        </SidebarHeader>
        <SidebarContent>
            <NavMain :items="navMain" />
        </SidebarContent>
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
