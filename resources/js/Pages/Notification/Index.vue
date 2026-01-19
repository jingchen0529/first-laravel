<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
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
import { RefreshCw, Trash2, Eye, Settings, Search } from 'lucide-vue-next';
import { ref, computed } from 'vue';

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

const selectedIds = ref<number[]>([]);
const showDeleteDialog = ref(false);
const keyword = ref('');

const isAllSelected = computed(() => {
    return props.list.data.length > 0 && selectedIds.value.length === props.list.data.length;
});

function toggleSelectAll() {
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.list.data.map(n => n.id);
    }
}

function toggleSelect(id: number) {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        selectedIds.value.splice(index, 1);
    } else {
        selectedIds.value.push(id);
    }
}

function isSelected(id: number) {
    return selectedIds.value.includes(id);
}

function refresh() {
    router.reload();
}

function markAllAsRead() {
    router.post('/account/notification/read-all', {}, {
        onSuccess: () => {
            selectedIds.value = [];
        }
    });
}

function confirmBatchDelete() {
    if (selectedIds.value.length === 0) return;
    showDeleteDialog.value = true;
}

function handleBatchDelete() {
    selectedIds.value.forEach(id => {
        router.delete(`/account/notification/${id}`, { preserveState: true });
    });
    showDeleteDialog.value = false;
    selectedIds.value = [];
}

function goToPage(page: number) {
    router.get('/account/notification', { page }, { preserveState: true });
}

function search() {
    router.get('/account/notification', { keyword: keyword.value }, { preserveState: true });
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleString('zh-CN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
}
</script>

<template>
    <Head title="通知中心" />
    <AuthenticatedLayout>
        <div class="flex max-w-7xl flex-col sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8">
                <!-- 操作栏 -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <Button variant="outline" size="sm" @click="refresh">
                            <RefreshCw class="w-4 h-4 mr-1" />
                            刷新
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            class="text-red-500 hover:text-red-600"
                            :disabled="selectedIds.length === 0"
                            @click="confirmBatchDelete"
                        >
                            <Trash2 class="w-4 h-4 mr-1" />
                            删除
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="markAllAsRead"
                        >
                            <Eye class="w-4 h-4 mr-1" />
                            全部标为已读
                        </Button>
                        <Button variant="default" size="sm" class="bg-orange-500 hover:bg-orange-600">
                            <Settings class="w-4 h-4 mr-1" />
                            通知设置
                        </Button>
                    </div>
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="keyword"
                            placeholder="搜索..."
                            class="w-48 h-9"
                            @keyup.enter="search"
                        />
                        <Button variant="outline" size="sm" @click="search">
                            <Search class="w-4 h-4" />
                        </Button>
                    </div>
                </div>

                <!-- 表格 -->
                <div class="border rounded-lg overflow-hidden">
                    <Table>
                        <TableHeader class="bg-gray-50">
                            <TableRow>
                                <TableHead class="w-12">
                                    <input
                                        type="checkbox"
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                </TableHead>
                                <TableHead class="font-medium">内容</TableHead>
                                <TableHead class="w-48 text-right font-medium">接收时间</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="notification in list.data"
                                :key="notification.id"
                                class="hover:bg-gray-50"
                            >
                                <TableCell class="py-3">
                                    <input
                                        type="checkbox"
                                        :checked="isSelected(notification.id)"
                                        @change="toggleSelect(notification.id)"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                </TableCell>
                                <TableCell class="py-3">
                                    <div class="flex items-start gap-2">
                                        <span
                                            v-if="!notification.read_at"
                                            class="shrink-0 px-2 py-0.5 text-xs bg-orange-500 text-white rounded"
                                        >
                                            未读
                                        </span>
                                        <span class="text-sm leading-relaxed">
                                            {{ notification.title }}，{{ notification.content }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell class="py-3 text-right text-sm text-gray-600">
                                    {{ formatDate(notification.created_at) }}
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="list.data.length === 0">
                                <TableCell colspan="3" class="text-center py-12 text-gray-500">
                                    暂无通知
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- 分页 -->
                <div v-if="list.total > 0" class="flex items-center justify-between text-sm text-gray-600 mt-4">
                    <span>共 {{ list.total }} 条记录，已选 {{ selectedIds.length }} 条</span>
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
                        确定要删除选中的 {{ selectedIds.length }} 条通知吗？
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>取消</AlertDialogCancel>
                    <AlertDialogAction @click="handleBatchDelete">删除</AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AuthenticatedLayout>
</template>
