<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Head, router } from '@inertiajs/vue3';
import { Check, CheckCheck, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

interface Notification {
    id: number;
    title: string;
    content: string;
    type: string;
    read_at: string | null;
    created_at: string;
}

interface PaginatedData {
    data: Notification[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    list: PaginatedData;
    unreadCount: number;
}>();

const deleteId = ref<number | null>(null);
const showDeleteDialog = ref(false);

function markAsRead(id: number) {
    router.post(`/account/notification/${id}/read`);
}

function markAllAsRead() {
    router.post('/account/notification/read-all');
}

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteDialog.value = true;
}

function handleDelete() {
    if (deleteId.value) {
        router.delete(`/account/notification/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteId.value = null;
            },
        });
    }
}

function goToPage(page: number) {
    router.get('/account/notification', { page }, { preserveState: true });
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleString('zh-CN');
}

function getTypeColor(type: string) {
    const colors: Record<string, string> = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500',
    };
    return colors[type] || 'bg-blue-500';
}
</script>

<template>
    <Head title="通知中心" />
    <AuthenticatedLayout>
        <div class="flex max-w-7xl flex-col sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">通知中心</h1>
                        <p class="text-sm text-muted-foreground mt-1">
                            共 {{ list.total }} 条通知，{{ unreadCount }} 条未读
                        </p>
                    </div>
                    <Button
                        v-if="unreadCount > 0"
                        variant="outline"
                        @click="markAllAsRead"
                    >
                        <CheckCheck class="w-4 h-4 mr-2" />
                        全部已读
                    </Button>
                </div>

                <div class="space-y-2">
                    <div v-if="list.data.length === 0" class="text-center text-muted-foreground py-16 border rounded-lg">
                        暂无通知
                    </div>

                    <div
                        v-for="notification in list.data"
                        :key="notification.id"
                        class="flex items-start gap-4 p-4 border rounded-lg hover:bg-muted/50 transition-colors"
                        :class="{ 'opacity-60': notification.read_at }"
                    >
                        <div
                            class="mt-1.5 h-2 w-2 rounded-full shrink-0"
                            :class="notification.read_at ? 'bg-gray-300' : getTypeColor(notification.type)"
                        ></div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-medium">{{ notification.title }}</p>
                                    <p class="text-sm text-muted-foreground mt-1">{{ notification.content }}</p>
                                    <p class="text-xs text-muted-foreground mt-2">{{ formatDate(notification.created_at) }}</p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <Button
                                        v-if="!notification.read_at"
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 w-8 p-0"
                                        @click="markAsRead(notification.id)"
                                        title="标记已读"
                                    >
                                        <Check class="w-4 h-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 w-8 p-0"
                                        @click="confirmDelete(notification.id)"
                                        title="删除"
                                    >
                                        <Trash2 class="w-4 h-4 text-red-500" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 分页 -->
                <div v-if="list.total > 0" class="flex items-center justify-between text-sm text-muted-foreground mt-4">
                    <span>共 {{ list.total }} 条记录</span>
                    <div class="flex items-center gap-2">
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="list.current_page <= 1"
                            @click="goToPage(list.current_page - 1)"
                        >
                            上一页
                        </Button>
                        <span>第 {{ list.current_page }} / {{ list.last_page }} 页</span>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="list.current_page >= list.last_page"
                            @click="goToPage(list.current_page + 1)"
                        >
                            下一页
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <AlertDialog v-model:open="showDeleteDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>确认删除</AlertDialogTitle>
                    <AlertDialogDescription>
                        确定要删除这条通知吗？
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>取消</AlertDialogCancel>
                    <AlertDialogAction @click="handleDelete">删除</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AuthenticatedLayout>
</template>
