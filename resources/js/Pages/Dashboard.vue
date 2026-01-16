<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Users, Bell, BellRing, TrendingUp } from 'lucide-vue-next';

interface Notification {
    id: number;
    title: string;
    content: string;
    type: string;
    read_at: string | null;
    created_at: string;
}

const props = defineProps<{
    stats: {
        userCount: number;
        todayUserCount: number;
        notificationCount: number;
        unreadNotificationCount: number;
    };
    recentNotifications: Notification[];
}>();

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleString('zh-CN');
}
</script>

<template>
    <Head title="仪表板" />

    <AuthenticatedLayout>
        <div class="flex max-w-7xl flex-col sm:px-6 lg:px-8">
            <!-- 统计卡片 -->
            <div class="p-4 sm:p-8">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border bg-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">用户总数</p>
                                <p class="text-3xl font-bold">{{ stats.userCount }}</p>
                            </div>
                            <Users class="h-10 w-10 text-muted-foreground" />
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">今日新增</p>
                                <p class="text-3xl font-bold">{{ stats.todayUserCount }}</p>
                            </div>
                            <TrendingUp class="h-10 w-10 text-muted-foreground" />
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">通知总数</p>
                                <p class="text-3xl font-bold">{{ stats.notificationCount }}</p>
                            </div>
                            <Bell class="h-10 w-10 text-muted-foreground" />
                        </div>
                    </div>

                    <div class="rounded-xl border bg-card p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">未读通知</p>
                                <p class="text-3xl font-bold">{{ stats.unreadNotificationCount }}</p>
                            </div>
                            <BellRing class="h-10 w-10 text-muted-foreground" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 最近通知 -->
            <div class="p-4 sm:p-8">
                <div class="rounded-xl border bg-card">
                    <div class="flex items-center justify-between border-b p-4">
                        <h2 class="text-lg font-semibold">最近通知</h2>
                        <Link href="/account/notification" class="text-sm text-primary hover:underline">
                            查看全部
                        </Link>
                    </div>
                    <div class="p-4">
                        <div v-if="recentNotifications.length === 0" class="text-center text-muted-foreground py-8">
                            暂无通知
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="notification in recentNotifications"
                                :key="notification.id"
                                class="flex items-start gap-3 rounded-lg p-3 hover:bg-muted/50"
                                :class="{ 'opacity-60': notification.read_at }"
                            >
                                <div
                                    class="mt-1 h-2 w-2 rounded-full shrink-0"
                                    :class="notification.read_at ? 'bg-gray-300' : 'bg-blue-500'"
                                ></div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium truncate">{{ notification.title }}</p>
                                    <p class="text-sm text-muted-foreground truncate">{{ notification.content }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">{{ formatDate(notification.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
