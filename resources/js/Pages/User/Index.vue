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
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus, Search } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
}

interface PaginatedData {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

const props = defineProps<{
    list: PaginatedData;
    filters: {
        keyword?: string;
    };
}>();

const keyword = ref(props.filters.keyword || '');
const deleteId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const selectedIds = ref<number[]>([]);

const isAllSelected = computed(() => {
    return props.list.data.length > 0 && selectedIds.value.length === props.list.data.length;
});

function toggleSelectAll() {
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.list.data.map(user => user.id);
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

function search() {
    router.get('/user', { keyword: keyword.value }, { preserveState: true });
}

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteDialog.value = true;
}

function handleDelete() {
    if (deleteId.value) {
        router.delete(`/user/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteId.value = null;
            },
        });
    }
}

function goToPage(page: number) {
    router.get('/user', { keyword: keyword.value, page }, { preserveState: true });
}

function formatDate(dateString: string) {
    return new Date(dateString).toLocaleString('zh-CN');
}
</script>

<template>
    <Head title="用户管理" />
    <AuthenticatedLayout>
        <div class="flex max-w-8xl flex-col sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <Link :href="route('user.create')">
                            <Button>
                                <Plus class="w-4 h-4 mr-2" />
                                新增
                            </Button>
                        </Link>
                    </div>
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="keyword"
                            placeholder="搜索用户名..."
                            class="w-64"
                            @keyup.enter="search"
                        />
                        <Button variant="outline" @click="search">
                            <Search class="w-4 h-4" />
                        </Button>
                    </div>
                </div>

                <div class="border rounded-lg">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-12">
                                    <input
                                        type="checkbox"
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                </TableHead>
                                <TableHead class="w-20">ID</TableHead>
                                <TableHead>用户名</TableHead>
                                <TableHead>邮箱</TableHead>
                                <TableHead>创建时间</TableHead>
                                <TableHead class="w-32 text-center">操作</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="user in list.data" :key="user.id">
                                <TableCell>
                                    <input
                                        type="checkbox"
                                        :checked="isSelected(user.id)"
                                        @change="toggleSelect(user.id)"
                                        class="h-4 w-4 rounded border-gray-300"
                                    />
                                </TableCell>
                                <TableCell>{{ user.id }}</TableCell>
                                <TableCell>{{ user.name }}</TableCell>
                                <TableCell>{{ user.email }}</TableCell>
                                <TableCell>{{ formatDate(user.created_at) }}</TableCell>
                                <TableCell class="text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <Link :href="route('user.edit', user.id)">
                                            <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                                                <Pencil class="w-4 h-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="user.id !== 1"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0"
                                            @click="confirmDelete(user.id)"
                                        >
                                            <Trash2 class="w-4 h-4 text-red-500" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex items-center justify-between text-sm text-muted-foreground mt-4">
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
                        删除后无法恢复，确定要删除该用户吗？
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
