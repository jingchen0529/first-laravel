<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuAction,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { Ellipsis, Folder, Share, Trash2 } from 'lucide-vue-next';

interface Member {
    name: string;
    url: string;
    isConnected: boolean;
}

interface Props {
    members: Member[];
}

defineProps<Props>();

const { isMobile } = useSidebar();
</script>

<template>
    <SidebarGroup class="group-data-[collapsible=icon]:hidden">
        <SidebarGroupLabel>项目成员</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="member in members" :key="member.name">
                <SidebarMenuButton as-child>
                    <a :href="member.url">
                        <div
                            :class="[
                                'h-2 w-2 rounded-full',
                                member.isConnected
                                    ? 'bg-green-500'
                                    : 'bg-gray-500',
                            ]"
                        ></div>
                        <span>{{ member.name }}</span>
                    </a>
                </SidebarMenuButton>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <SidebarMenuAction showOnHover as-child>
                            <Ellipsis />
                            <span class="sr-only">更多</span>
                        </SidebarMenuAction>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        class="w-48"
                        :side="isMobile ? 'bottom' : 'right'"
                        :align="isMobile ? 'end' : 'start'"
                    >
                        <DropdownMenuItem>
                            <Folder class="text-muted-foreground" />
                            <span>查看成员</span>
                        </DropdownMenuItem>
                        <DropdownMenuItem>
                            <Share class="text-muted-foreground" />
                            <span>分享成员</span>
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem>
                            <Trash2 class="text-muted-foreground" />
                            <span>删除成员</span>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
